@extends('admin.layout.index')
@section('title', '管理员列表')
@section('content')
    <div id="vue" class="pt-3 pt-sm-0">
        <div class="card">
            <div class="card-header">
                管理员列表
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>UID</th>
                            <th>用户名</th>
                            <th>邮箱</th>
                            <th>状态</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody v-cloak>
                        <tr v-for="(row,i) in data.data" :key="i">
                            <td>@{{ row.uid }}</td>
                            <td>@{{ row.username }}</td>
                            <td>@{{ row.email }}</td>
                            <td>
                                <span v-if="row.status===0">已禁用</span>
                                <span v-else-if="row.status===1">待认证</span>
                                <span v-else-if="row.status===2">已认证</span>
                            </td>
                            <td>@{{ row.created_at }}</td>
                            <td>
                                <a class="btn btn-sm btn-secondary" @click="unsetAdmin(row.uid)">取消管理员</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer pb-0 text-center">
                @include('admin.layout.pagination')
            </div>
        </div>
    </div>
@endsection
@section('foot')
    <script>
        new Vue({
            el: '#vue',
            data: {
                search: { page: 1 },
                data: {}
            },
            methods: {
                getList: function(page){
                    var vm = this;
                    vm.search.page = typeof page === 'undefined' ? vm.search.page : page;
                    this.$post('/admin/user', vm.search, { action: 'adminSelect' })
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.data = data.data
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        });
                },
                unsetAdmin: function(uid){
                    if(!confirm('确认取消管理员？')) return;
                    if(!confirm('再次确认：将把该用户降级为普通组')) return;
                    var vm = this;
                    this.$post('/admin/user', { action: 'setAdmin', uid: uid, act: 0 })
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.getList();
                                vm.$message(data.message, 'success');
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        });
                }
            },
            mounted: function(){
                this.getList();
            }
        })
    </script>
@endsection