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
    var token = $('meta[name=csrf-token]').attr('content')
    $.post('/admin', {action:'orderList', type:'point', _token: token}, function(res){
        if(res && res.status===0 && res.data && Array.isArray(res.data.list)){
            var frag = document.createDocumentFragment()
            res.data.list.forEach(function(i){
                var tr = document.createElement('tr')
                function tdText(val){
                    var td=document.createElement('td');
                    $(td).text(val==null?'':String(val))
                    return td
                }
                tr.appendChild(tdText(i.id))
                tr.appendChild(tdText(i.order_no))
                tr.appendChild(tdText(i.user))
                tr.appendChild(tdText(i.amount))
                tr.appendChild(tdText(i.point))
                tr.appendChild(tdText(i.status==1?'已入账':'未支付'))
                tr.appendChild(tdText(i.pay_type))
                tr.appendChild(tdText(i.trade_no||''))
                tr.appendChild(tdText(i.created_at))
                frag.appendChild(tr)
            })
            document.getElementById('list').innerHTML=''
            document.getElementById('list').appendChild(frag)
        }else{
            layer.msg((res&&res.message)||'加载失败')
        }
    }).fail(function(){
        layer.msg('请求失败')
    })
})
</script>
@endsection