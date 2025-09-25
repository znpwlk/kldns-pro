@extends('home.layout.index')
@section('title','支付结果')
@section('content')
<div class="container" style="padding: 24px 0;">
    <h2 style="font-weight:600;">{{$msg}}</h2>
    <p>如已支付成功但未到账，请稍后刷新积分明细。</p>
    <a href="/home/point" class="btn btn-primary">返回积分明细</a>
</div>
@endsection