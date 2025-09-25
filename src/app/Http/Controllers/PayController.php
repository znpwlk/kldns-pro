<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PointOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{
    public function post(Request $request)
    {
        $action = $request->input('action');
        if ($action === 'create' || $action === 'createOrder') {
            return $this->create($request);
        }
        if ($action === 'notify') {
            return $this->notify($request);
        }
        if ($action === 'return') {
            return $this->return($request);
        }
        return response()->json(['status' => 404, 'message' => '页面不存在']);
    }

    protected function create(Request $request)
    {
        $user = $request->user();
        $uid = intval($user->id ?? 0);
        if ($uid <= 0) {
            return response()->json(['status' => 401, 'message' => '未登录']);
        }
        $switch = intval(config('sys.user.point.buy_switch'));
        if ($switch !== 1) {
            return response()->json(['status' => 403, 'message' => '暂不支持购买积分']);
        }
        $ratio = max(1, intval(config('sys.user.point.buy_ratio', 1)));
        $amountRaw = trim((string)$request->input('amount'));
        if ($amountRaw === '' || !preg_match('/^(?:0|[1-9]\d*)(?:\.\d{1,2})?$/', $amountRaw)) {
            return response()->json(['status' => 422, 'message' => '金额格式不合法']);
        }
        $amount = (float)number_format((float)$amountRaw, 2, '.', '');
        if ($amount < 0.01 || $amount > 1000000) {
            return response()->json(['status' => 422, 'message' => '金额超出允许范围']);
        }
        $payApi = trim((string)config('sys.epay.api'));
        $pid = trim((string)config('sys.epay.pid'));
        $key = (string)config('sys.epay.key');
        if ($payApi === '' || $pid === '' || $key === '') {
            return response()->json(['status' => 500, 'message' => '支付未配置']);
        }
        $orderNo = PointOrder::createOrderNo();
        $point = (int)round($amount * $ratio);
        DB::beginTransaction();
        try {
            PointOrder::create([
                'order_no'   => $orderNo,
                'uid'        => $uid,
                'amount'     => $amount,
                'point'      => $point,
                'status'     => 0,
                'pay_type'   => 'epay',
                'ip'         => $request->ip(),
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('create point order failed', ['err' => $e->getMessage()]);
            return response()->json(['status' => 500, 'message' => '下单失败']);
        }
        $data = [
            'pid'          => $pid,
            'type'         => 'alipay',
            'out_trade_no' => $orderNo,
            'notify_url'   => url('/pay?action=notify'),
            'return_url'   => url('/pay?action=return'),
            'name'         => '积分充值',
            'money'        => number_format($amount, 2, '.', ''),
            'sitename'     => config('sys.web.name', '充值'),
        ];
        ksort($data);
        $signStr = '';
        foreach ($data as $k => $v) {
            if ($v === '' || $v === null) continue;
            $signStr .= $k . '=' . $v . '&';
        }
        $signStr = rtrim($signStr, '&');
        $sign = md5($signStr . $key);
        $data['sign'] = $sign;
        $data['sign_type'] = 'MD5';
        $payUrl = rtrim($payApi, '/') . '/submit.php?' . http_build_query($data);
        return response()->json(['status' => 0, 'message' => 'ok', 'data' => ['pay_url' => $payUrl, 'order_no' => $orderNo]]);
    }

    protected function verifyEpay(array $params): bool
    {
        $key = (string)config('sys.epay.key');
        if ($key === '') return false;
        $sign = $params['sign'] ?? '';
        $signType = strtoupper((string)($params['sign_type'] ?? 'MD5'));
        unset($params['sign'], $params['sign_type']);
        ksort($params);
        $str = '';
        foreach ($params as $k => $v) {
            if ($v === '' || $v === null) continue;
            $str .= $k . '=' . $v . '&';
        }
        $str = rtrim($str, '&');
        $calc = $signType === 'MD5' ? md5($str + $key) : '';
        // 由于 PHP 的 + 会在字符串上被当作数值运算，以上一行为笔误，修正：
        $calc = $signType === 'MD5' ? md5($str . $key) : $calc;
        return hash_equals($calc, $sign);
    }

    protected function notify(Request $request)
    {
        $params = $request->all();
        if (!$this->verifyEpay($params)) {
            Log::warning('epay notify sign invalid', ['params' => $params]);
            return response('fail', 200);
        }
        $outTradeNo = (string)($params['out_trade_no'] ?? '');
        $tradeNo = (string)($params['trade_no'] ?? '');
        $status = (string)($params['trade_status'] ?? '');
        if ($outTradeNo === '' || $tradeNo === '' || $status !== 'TRADE_SUCCESS') {
            return response('fail', 200);
        }
        $order = PointOrder::where('order_no', $outTradeNo)->lockForUpdate()->first();
        if (!$order) return response('fail', 200);
        if (intval($order->status) === 1) return response('success', 200);
        DB::beginTransaction();
        try {
            $order->status = 1;
            $order->trade_no = $tradeNo;
            $order->notify_data = json_encode($params, JSON_UNESCAPED_UNICODE);
            $order->save();
            User::point($order->uid, 'add', intval($order->point), '积分充值');
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('notify handle error', ['err' => $e->getMessage()]);
            return response('fail', 200);
        }
        return response('success', 200);
    }

    protected function return(Request $request)
    {
        $params = $request->all();
        $ok = $this->verifyEpay($params);
        $msg = $ok ? '支付成功' : '校验失败';
        return view('home.payReturn', ['msg' => $msg]);
    }
}