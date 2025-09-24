<?php
namespace App\Http\Controllers;

use App\Models\PointOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayController extends Controller
{
    public function post(Request $request)
    {
        $action = $request->post('action');
        switch ($action) {
            case 'createOrder':
                return $this->createOrder($request);
            case 'notify':
                return $this->notify($request);
            case 'return':
                return $this->payReturn($request);
        }
        abort(404);
    }

    private function createOrder(Request $request)
    {
        if (!config('sys.user.point.buy_switch')) {
            return ['status' => 1, 'message' => '暂未开放积分充值'];
        }
        $amount = floatval($request->post('amount'));
        if ($amount <= 0) {
            return ['status' => 1, 'message' => '充值金额不正确'];
        }
        $ratio = floatval(config('sys.user.point.buy_ratio', 1));
        $point = intval(round($amount * $ratio));
        $order = PointOrder::create([
            'order_no' => PointOrder::genOrderNo(),
            'uid' => auth()->id(),
            'amount' => $amount,
            'point' => $point,
            'status' => 0,
            'ip' => $request->ip(),
        ]);
        $params = [
            'pid' => config('sys.epay.pid'),
            'type' => 'alipay',
            'out_trade_no' => $order->order_no,
            'notify_url' => url('/pay'),
            'return_url' => url('/pay'),
            'name' => '积分充值',
            'money' => number_format($amount, 2, '.', ''),
        ];
        $params['sign'] = $this->sign($params);
        $params['sign_type'] = 'MD5';
        return ['status' => 0, 'message' => 'ok', 'data' => [
            'pay_url' => rtrim(config('sys.epay.api'), '/') . '/submit.php?' . http_build_query($params)
        ]];
    }

    private function notify(Request $request)
    {
        $data = $request->post();
        if (!$this->verify($data)) {
            return 'fail';
        }
        $orderNo = $data['out_trade_no'] ?? '';
        $tradeNo = $data['trade_no'] ?? '';
        $status = $data['trade_status'] ?? '';
        if ($status !== 'TRADE_SUCCESS') {
            return 'success';
        }
        $order = PointOrder::query()->where('order_no', $orderNo)->first();
        if (!$order) return 'success';
        if (intval($order->status) === 1) return 'success';
        DB::beginTransaction();
        try {
            $order->status = 1;
            $order->trade_no = $tradeNo;
            $order->notify_data = json_encode($data, JSON_UNESCAPED_UNICODE);
            $order->save();
            User::point($order->uid, $order->point, '充值入账');
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return 'fail';
        }
        return 'success';
    }

    private function payReturn(Request $request)
    {
        $data = $request->all();
        $ok = $this->verify($data) && ($data['trade_status'] ?? '') === 'TRADE_SUCCESS';
        return redirect('/home')->with('message', $ok ? '充值成功' : '充值校验失败');
    }

    private function sign(array $data): string
    {
        ksort($data);
        $arg = '';
        foreach ($data as $k => $v) {
            if ($v !== '' && $k !== 'sign' && $k !== 'sign_type') {
                $arg .= $k . '=' . $v . '&';
            }
        }
        $arg = rtrim($arg, '&');
        return md5($arg . config('sys.epay.key'));
    }

    private function verify(array $data): bool
    {
        if (!isset($data['sign'])) return false;
        $sign = $data['sign'];
        unset($data['sign'], $data['sign_type']);
        return $sign === $this->sign($data);
    }
}