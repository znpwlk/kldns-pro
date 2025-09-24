@extends('home.layout.index')
@section('title','充值订单')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">查看我的充值订单</h4>
            <div class="mb-2 text-muted">仅展示最近200条订单</div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>订单号</th>
                            <th>金额</th>
                            <th>积分</th>
                            <th>状态</th>
                            <th>支付渠道</th>
                            <th>第三方单号</th>
                            <th>创建时间</th>
                        </tr>
                    </thead>
                    <tbody id="order_tbody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url:'/pay',
        method:'POST',
        data:{action:'myOrders'},
        headers:{'X-CSRF-TOKEN':token}
    }).done(function(res){
        if(res && res.code === 0 && Array.isArray(res.data)){
            var html='';
            for(var i=0;i<res.data.length;i++){
                var it=res.data[i];
                html += '<tr>'+
                        '<td>'+escapeHtml(it.order_no||'')+'</td>'+
                        '<td>'+formatAmount(it.amount)+'</td>'+
                        '<td>'+parseInt(it.point||0)+'</td>'+
                        '<td>'+escapeHtml(it.status_text||'')+'</td>'+
                        '<td>'+escapeHtml(it.pay_type||'')+'</td>'+
                        '<td>'+escapeHtml(it.trade_no||'')+'</td>'+
                        '<td>'+escapeHtml(it.created_at||'')+'</td>'+
                        '</tr>';
            }
            $('#order_tbody').html(html||'<tr><td colspan="7" class="text-muted">暂无数据</td></tr>');
        }else{
            $('#order_tbody').html('<tr><td colspan="7" class="text-muted">加载失败</td></tr>');
        }
    }).fail(function(){
        $('#order_tbody').html('<tr><td colspan="7" class="text-muted">网络错误</td></tr>');
    });
});
function escapeHtml(s){
    return String(s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c];});
}
function formatAmount(a){
    var n = Number(a);
    if(!isFinite(n)) return '0.00';
    return n.toFixed(2);
}
</script>
@endsection