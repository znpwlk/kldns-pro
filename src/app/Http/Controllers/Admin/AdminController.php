<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2019/4/14
 * Time: 16:41
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function post(Request $request)
    {
        $action = $request->post('action');
        switch ($action) {
            case 'profile':
                return $this->profile($request);
            case 'orderList':
                $type = $request->post('type');
                if ($type === 'point') {
                    $list = \App\Models\PointOrder::query()->orderByDesc('id')->limit(200)->get();
                    return ['status' => 0, 'data' => ['list' => $list->map(function($i){
                        return [
                            'id' => $i->id,
                            'order_no' => $i->order_no,
                            'user' => optional(\App\Models\User::query()->find($i->uid))->username,
                            'amount' => $i->amount,
                            'point' => $i->point,
                            'status' => $i->status,
                            'pay_type' => $i->pay_type,
                            'trade_no' => $i->trade_no,
                            'created_at' => $i->created_at,
                        ];
                    })]];
                }
                return ['status' => 1, 'message' => '类型错误'];
            default:
                return ['status' => -1, 'message' => '对不起，此操作不存在！'];
        }
    }

    private function profile(Request $request)
    {
        $result = ['status' => -1];
        $old_password = $request->post('old_password');
        $new_password = $request->post('new_password');
        if (strlen($old_password) < 5) {
            $result['message'] = '旧密码验证失败';
        } elseif (!Hash::check($old_password, Auth::guard('admin')->user()->password)) {
            $result['message'] = '旧密码验证失败';
        } elseif (strlen($new_password) < 5) {
            $result['message'] = '新密码太简单';
        } else {
            if (User::where('uid', Auth::guard('admin')->id())->update([
                'password' => Hash::make($new_password),
                'sid' => md5(uniqid() . Str::random())
            ])) {
                $result = ['status' => 0, 'message' => '修改成功'];
            } else {
                $result['message'] = '修改失败，请稍后再试！';
            }
        }
        return $result;
    }
}