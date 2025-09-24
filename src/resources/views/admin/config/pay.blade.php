@extends('admin.layout.index')
@section('title', '支付设置')
@section('content')
<div id="vue" class="pt-3 pt-sm-0">
    <div class="toolbar" style="position:sticky;top:0;z-index:2;background:rgba(255,255,255,0.7);backdrop-filter:blur(6px);border-bottom:1px solid #e9ecef">
        <div class="container-fluid px-0 py-2 d-flex align-items-center">
            <div class="mr-3" style="font-weight:600;letter-spacing:.5px">支付设置</div>
            <a class="btn btn-sm btn-outline-primary" @click="form('epay')">保存</a>
        </div>
    </div>
    <div class="container-fluid px-0">
        <form id="form-epay" class="mt-3">
            <input type="hidden" name="action" value="config">
            <div class="row no-gutters">
                <div class="col-12 col-lg-7 pr-lg-4">
                    <div style="border-left:3px solid #cbd3da;padding-left:12px;margin-bottom:16px;color:#6c757d">基础</div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">允许购买积分</label>
                        <div class="col-sm-9">
                            <select name="user[point][buy_switch]" class="form-control">
                                <option value="1" {{ intval(config('sys.user.point.buy_switch'))===1?'selected':'' }}>开启</option>
                                <option value="0" {{ intval(config('sys.user.point.buy_switch'))===0?'selected':'' }}>关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">比例（1元兑换）</label>
                        <div class="col-sm-9">
                            <input type="number" step="1" min="1" name="user[point][buy_ratio]" class="form-control" value="{{ config('sys.user.point.buy_ratio', 1) }}">
                        </div>
                    </div>
                    <div style="border-left:3px solid #cbd3da;padding-left:12px;margin:24px 0 16px;color:#6c757d">易支付</div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">接口地址</label>
                        <div class="col-sm-9">
                            <input type="text" name="epay[api]" class="form-control" value="{{ config('sys.epay.api') }}" placeholder="https://你的易支付域名">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">商户ID</label>
                        <div class="col-sm-9">
                            <input type="text" name="epay[pid]" class="form-control" value="{{ config('sys.epay.pid') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">商户密钥</label>
                        <div class="col-sm-9">
                            <input type="password" name="epay[key]" class="form-control" value="{{ config('sys.epay.key') }}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5 mt-4 mt-lg-0">
                    <div class="reco-header" style="border-left:3px solid #cbd3da;padding-left:12px;margin-bottom:10px;color:#6c757d">推荐易支付商</div>
                    <div id="epay_reco" style="background:rgba(255,255,255,0.6);backdrop-filter:blur(4px);border-top:1px solid #e9ecef;border-bottom:1px solid #e9ecef;padding:10px 12px">
                        <div class="text-muted">加载中...</div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('foot')
<script>
new Vue({
    el:'#vue',
    data:{},
    methods:{
        form:function(id){
            var vm=this;
            this.$post('/admin/config', $('#form-'+id).serialize()).then(function(data){
                if(data.status===0){vm.$message(data.message,'success');}else{vm.$message(data.message,'error');}
            });
        },
        loadEpayReco:function(){
            var box=document.getElementById('epay_reco'); if(!box) return;
            var xhr=new XMLHttpRequest();
            xhr.open('GET','https://znpwlk.github.io/kldns-api/pay.html',true);
            xhr.onreadystatechange=function(){
                if(xhr.readyState===4){
                    if(xhr.status===200){
                        try{
                            var text = xhr.responseText || '';
                            var lines = text.split(/\r?\n/).map(function(s){return s.trim();}).filter(function(s){return s.length>0;});
                            var items = [];
                            var current = null;
                            var nameMatch;
                            for(var i=0;i<lines.length;i++){
                                var line = lines[i];
                                if((nameMatch = line.match(/^\[(\d+)\]\s*(.+)$/))){
                                    if(current){ items.push(current); }
                                    current = { name: nameMatch[2], desc: [], link: '', icon: '' };
                                }else if(/^链接\s*(https?:\/\/\S+)/i.test(line)){
                                    var u = line.replace(/^链接\s*/i,'').trim();
                                    current && (current.link = u);
                                }else if(/^图标\s*(https?:\/\/\S+)/i.test(line)){
                                    var ic = line.replace(/^图标\s*/i,'').trim();
                                    current && (current.icon = ic);
                                }else{
                                    current && current.desc.push(line);
                                }
                            }
                            if(current){ items.push(current); }
                            while(box.firstChild){ box.removeChild(box.firstChild); }
                            if(!items.length){ box.appendChild(document.createTextNode('暂无推荐')); return; }
                            var frag = document.createDocumentFragment();
                            var urlOk = function(u){ return /^https?:\/\//i.test(u||''); };
                            items.forEach(function(it){
                                var row = document.createElement('div');
                                row.className='d-flex align-items-center py-1';
                                row.style.lineHeight='1.6';
                                if(urlOk(it.icon)){
                                    var img=document.createElement('img');
                                    img.width=18; img.height=18; img.alt=''; img.className='mr-2';
                                    img.src=it.icon;
                                    img.addEventListener('error', function(){ this.style.display='none'; });
                                    row.appendChild(img);
                                }
                                var a = document.createElement('a');
                                a.className='mr-2'; a.target='_blank'; a.rel='noopener';
                                a.textContent = (it.name||'').slice(0,50);
                                if(urlOk(it.link)) a.href = it.link; else a.href = 'javascript:void(0)';
                                row.appendChild(a);
                                var span = document.createElement('span');
                                span.className='text-muted small';
                                var desc = (it.desc||[]).join(' ');
                                span.textContent = desc.length>120? (desc.slice(0,117)+'...') : desc;
                                row.appendChild(span);
                                frag.appendChild(row);
                            });
                            box.appendChild(frag);
                        }catch(e){
                            box.textContent='暂无推荐';
                        }
                    }else{ box.textContent='无法获取推荐'; }
                }
            };
            xhr.send();
        }
    },
    mounted:function(){ this.loadEpayReco(); }
});
</script>
@endsection