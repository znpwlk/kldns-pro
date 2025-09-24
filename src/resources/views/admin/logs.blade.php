@extends('admin.layout.index')
@section('title', '日志管理')
@section('head')
    <style>
        pre.log-view{background:#0b0b0b;color:#e0e0e0;padding:10px;border-radius:4px;max-height:60vh;overflow:auto;white-space:pre-wrap;word-break:break-all}
        .file-list{max-height:50vh;overflow:auto}
    </style>
@endsection
@section('content')
    <div id="vue" class="pt-3 pt-sm-0">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <i class="fa fa-file-alt"></i> 日志列表
                </div>
                <div class="form-inline">
                    <label class="mr-2">查看行数</label>
                    <input type="number" class="form-control form-control-sm" v-model.number="lines" min="50" max="5000" step="50">
                    <button class="btn btn-info btn-sm ml-2" @click="getList()"><i class="fa fa-sync"></i> 刷新</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="file-list list-group">
                            <a href="javascript:void(0)" class="list-group-item list-group-item-action"
                               v-for="(f,i) in files" :key="i" :class="{active:selected && selected.name===f.name}"
                               @click="openFile(f.name)">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><i class="fa fa-file"></i> @{{ f.name }}</h6>
                                    <small>@{{ formatSize(f.size) }} · @{{ f.updated_at }}</small>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div v-if="selected">
                            <div class="mb-2">
                                <strong>@{{ selected.name }}</strong>
                                <a class="btn btn-sm btn-outline-secondary ml-2" :href="'/admin/logs/download?name=' + encodeURIComponent(selected.name)">
                                    <i class="fa fa-download"></i> 下载
                                </a>
                                <button class="btn btn-sm btn-warning ml-2" @click="doTruncate(selected.name)"><i class="fa fa-eraser"></i> 清空</button>
                                <button class="btn btn-sm btn-danger ml-2" @click="doDelete(selected.name)"><i class="fa fa-trash"></i> 删除</button>
                            </div>
                            <pre class="log-view" v-text="content"></pre>
                        </div>
                        <div v-else class="text-muted">选择左侧日志文件查看内容</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot')
<script>
new Vue({
    el:'#vue',
    data:{
        files:[],
        selected:null,
        content:'',
        lines:500,
        loading:false
    },
    mounted(){
        this.getList();
    },
    methods:{
        formatSize(s){
            if(s<1024) return s+' B';
            if(s<1024*1024) return (s/1024).toFixed(1)+' KB';
            if(s<1024*1024*1024) return (s/1024/1024).toFixed(1)+' MB';
            return (s/1024/1024/1024).toFixed(1)+' GB';
        },
        getList(){
            var that=this;
            $.post('/admin/logs', {action:'list', _token:$('meta[name="csrf-token"]').attr('content')}, function (ret) {
                if(ret.status===0){
                    that.files = ret.data || [];
                    if (!that.selected && that.files.length>0){
                        that.openFile(that.files[0].name);
                    }
                } else {
                    layer.alert(ret.message||'加载失败');
                }
            }, 'json');
        },
        openFile(name){
            this.selected = {name:name};
            this.loadContent(name);
        },
        loadContent(name){
            var that=this;
            that.loading = true;
            $.post('/admin/logs', {action:'view', name:name, lines:that.lines, _token:$('meta[name="csrf-token"]').attr('content')}, function (ret) {
                that.loading = false;
                if(ret.status===0){
                    // 输出为纯文本，不做 HTML 渲染，防 XSS
                    that.content = ret.data.content || '';
                } else {
                    layer.alert(ret.message||'读取失败');
                }
            }, 'json');
        },
        doTruncate(name){
            var that=this;
            layer.confirm('确定要清空该日志吗？此操作不可恢复。', function(){
                $.post('/admin/logs', {action:'truncate', name:name, _token:$('meta[name="csrf-token"]').attr('content')}, function (ret) {
                    if(ret.status===0){
                        layer.msg('已清空');
                        that.loadContent(name);
                        that.getList();
                    } else {
                        layer.alert(ret.message||'清空失败');
                    }
                }, 'json');
            });
        },
        doDelete(name){
            var that=this;
            layer.confirm('确定要删除该日志文件吗？此操作不可恢复。', function(){
                $.post('/admin/logs', {action:'delete', name:name, _token:$('meta[name="csrf-token"]').attr('content')}, function (ret) {
                    if(ret.status===0){
                        layer.msg('已删除');
                        that.content='';
                        that.selected=null;
                        that.getList();
                    } else {
                        layer.alert(ret.message||'删除失败');
                    }
                }, 'json');
            });
        }
    }
});
</script>
@endsection
