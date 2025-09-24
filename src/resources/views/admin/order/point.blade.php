@extends('admin.layout.index')
@section('title', '积分订单')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h5>积分订单</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>订单号</th>
                            <th>用户</th>
                            <th>金额</th>
                            <th>积分</th>
                            <th>状态</th>
                            <th>渠道</th>
                            <th>交易号</th>
                            <th>时间</th>
                        </tr>
                    </thead>
                    <tbody id="list"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    $.post('/admin', {action:'orderList', type:'point', _token: $('meta[name=csrf-token]').attr('content')}, function(res){
        if(res.status===0){
            var html=''
            res.data.list.forEach(function(i){
                html += '<tr>'+
                    '<td>'+i.id+'</td>'+
                    '<td>'+i.order_no+'</td>'+
                    '<td>'+i.user+'</td>'+
                    '<td>'+i.amount+'</td>'+
                    '<td>'+i.point+'</td>'+
                    '<td>'+(i.status==1?'已入账':'未支付')+'</td>'+
                    '<td>'+i.pay_type+'</td>'+
                    '<td>'+(i.trade_no||'')+'</td>'+
                    '<td>'+i.created_at+'</td>'+
                '</tr>'
            })
            $('#list').html(html)
        }else{
            layer.msg(res.message||'加载失败')
        }
    })
})
</script>
@endsection