@extends('home.layout.index')
@section('title', '购买积分')
@section('head')
<style>
.page-shell{padding:24px}
.page-title{font-size:20px;color:#333;letter-spacing:.3px}
.buy-panel{margin-top:16px;padding:16px 0;border-top:1px solid rgba(0,0,0,.06)}
.buy-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}
.buy-item label{color:#555}
.buy-item input{width:240px;border:1px solid rgba(0,0,0,.12);padding:8px 10px;border-radius:2px}
.buy-actions{margin-top:24px}
.btn-primary{background:#3c6;border:none;padding:8px 16px;color:#fff;border-radius:2px}
.hint{color:#777;margin-left:12px}
</style>
@endsection
@section('content')
<div class="page-shell">
    <div class="page-title">购买积分</div>
    <div class="buy-panel">
        <div class="buy-grid">
            <div class="buy-item">
                <label>充值金额</label>
                <input type="number" min="1" step="0.01" id="amount" placeholder="输入金额" />
            </div>
            <div class="buy-item">
                <label>兑换比例</label>
                <div>{{ config('sys.user.point.buy_ratio', 1) }} 积分 / 1 元<span class="hint">比例由系统设置</span></div>
            </div>
        </div>
        <div class="buy-actions">
            <button class="btn-primary" onclick="doPay()">发起支付</button>
            <span class="hint">支付由易支付渠道完成</span>
        </div>
    </div>
</div>
<script>
function doPay(){
    var amount = document.getElementById('amount').value
    if(!amount || parseFloat(amount)<=0){layer.msg('请输入正确金额');return}
    $.post('/pay', {action:'createOrder', amount: amount, _token: $('meta[name=csrf-token]').attr('content')}, function(res){
        if(res.status===0){
            location.href = res.data.pay_url
        }else{
            layer.msg(res.message||'请求失败')
        }
    })
}
</script>
@endsection