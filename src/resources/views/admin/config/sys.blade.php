@extends('admin.layout.index')
@section('title', '系统配置')
@section('content')
    <div id="vue" class="pt-3 pt-sm-0 row">
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    站点设置
                </div>
                <div class="card-body">
                    <form id="form-web">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">站点名称</label>
                            <div class="col-sm-9">
                                <input type="text" name="web[name]" class="form-control" placeholder="输入站点名称"
                                       value="{{ function_exists('safe_config_output') ? safe_config_output('sys.web.name') : (is_scalar(config('sys.web.name')) ? config('sys.web.name') : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">首页标题</label>
                            <div class="col-sm-9">
                                <input type="text" name="web[title]" class="form-control" placeholder="输入首页标题"
                                       value="{{ function_exists('safe_config_output') ? safe_config_output('sys.web.title') : (is_scalar(config('sys.web.title')) ? config('sys.web.title') : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">网站关键词</label>
                            <div class="col-sm-9">
                                <input type="text" name="web[keywords]" class="form-control" placeholder="输入网站关键词"
                                       value="{{ function_exists('safe_config_output') ? safe_config_output('sys.web.keywords') : (is_scalar(config('sys.web.keywords')) ? config('sys.web.keywords') : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">网站描述</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="web[description]" placeholder="输入网站描述"
                                >{{ function_exists('safe_config_output') ? safe_config_output('sys.web.description') : (is_scalar(config('sys.web.description')) ? config('sys.web.description') : '') }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">首页代码</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="html_header" placeholder="输入首页代码（支持html）" rows="5"
                                >{!! config('sys.html_header') !!}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">用户公告</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="html_home" placeholder="输入首页代码（支持html）" rows="5"
                                >{!! config('sys.html_home') !!}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">首页链接</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="index_urls" placeholder="输入首页顶部链接" rows="3"
                                >{!! config('sys.index_urls') !!}</textarea>
                                <div class="input_tips">
                                    格式：链接名称|链接地址  一行一条
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('web')">保存</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    用户配置
                </div>
                <div class="card-body">
                    <form id="form-user">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">开启注册</label>
                            <div class="col-sm-9">
                                <select name="user[reg]" :value="{{ config('sys.user.reg',0) }}" class="form-control">
                                <option value="0">关闭注册</option>
                                <option value="1">开启注册</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">邮箱认证</label>
                            <div class="col-sm-9">
                                <select name="user[email]" :value="{{ config('sys.user.email',0) }}"
                                        class="form-control">
                                    <option value="0">不需要认证</option>
                                    <option value="1">需要认证</option>
                                </select>
                                <div class="input_tips">开启认证，则用户注册后是待认证状态，系统会发送一封认证邮件，用户点击邮件中链接进行认证！</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">注册赠送积分</label>
                            <div class="col-sm-9">
                                <input type="number" name="user[point]" class="form-control" placeholder="输入注册赠送积分"
                                       value="{{ function_exists('safe_config_output') ? safe_config_output('sys.user.point',0) : (is_array($v = config('sys.user.point', 0)) ? 0 : $v) }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('user')">保存</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    记录限制
                </div>
                <div class="card-body">
                    <img src="/images/record-limit.svg" alt="记录限制说明" style="width:100%;opacity:.9">
                    <form id="form-record">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">记录名最大长度</label>
                            <div class="col-sm-9">
                                <input type="number" name="record[name_max_len]" class="form-control" placeholder="输入最大长度"
                                       value="{{ function_exists('safe_config_output') ? safe_config_output('sys.record.name_max_len',63) : (is_array($v = config('sys.record.name_max_len', 63)) ? 63 : $v) }}">
                                <div class="input_tips">主机记录（例如 www、api）允许的最大字符数，通常不超过 63；过长会影响解析兼容性。</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">禁止CNAME指向根域</label>
                            <div class="col-sm-9">
                                <select name="record[forbid_cname_root]" class="form-control">
                                    <option value="0" @if(config('sys.record.forbid_cname_root',1)==0) selected @endif>关闭</option>
                                    <option value="1" @if(config('sys.record.forbid_cname_root',1)==1) selected @endif>开启</option>
                                </select>
                                <div class="input_tips">开启后，CNAME 不能指向顶级域名（例如 example.com），避免不规范解析与循环。</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">禁止私有IP</label>
                            <div class="col-sm-9">
                                <select name="record[forbid_private_ip]" class="form-control">
                                    <option value="0" @if(config('sys.record.forbid_private_ip',1)==0) selected @endif>关闭</option>
                                    <option value="1" @if(config('sys.record.forbid_private_ip',1)==1) selected @endif>开启</option>
                                </select>
                                <div class="input_tips">开启后，A/AAAA 记录不得指向内网地址（如 10.x、192.168.x、172.16-31.x），提升安全性与正确性。</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">禁止非法IPv4</label>
                            <div class="col-sm-9">
                                <select name="record[forbid_invalid_ipv4]" class="form-control">
                                    <option value="0" @if(config('sys.record.forbid_invalid_ipv4',1)==0) selected @endif>关闭</option>
                                    <option value="1" @if(config('sys.record.forbid_invalid_ipv4',1)==1) selected @endif>开启</option>
                                </select>
                                <div class="input_tips">开启后，A 记录必须是合法的 IPv4 地址（如 1.2.3.4），非法格式会被拒绝。</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">禁止非法IPv6</label>
                            <div class="col-sm-9">
                                <select name="record[forbid_invalid_ipv6]" class="form-control">
                                    <option value="0" @if(config('sys.record.forbid_invalid_ipv6',1)==0) selected @endif>关闭</option>
                                    <option value="1" @if(config('sys.record.forbid_invalid_ipv6',1)==1) selected @endif>开启</option>
                                </select>
                                <div class="input_tips">开启后，AAAA 记录必须是合法的 IPv6 地址（如 2001:db8::1），非法格式会被拒绝。</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">禁止CNAME指向自身</label>
                            <div class="col-sm-9">
                                <select name="record[forbid_cname_self]" class="form-control">
                                    <option value="0" @if(config('sys.record.forbid_cname_self',1)==0) selected @endif>关闭</option>
                                    <option value="1" @if(config('sys.record.forbid_cname_self',1)==1) selected @endif>开启</option>
                                </select>
                                <div class="input_tips">开启后，CNAME 不允许指向同名主机记录本身，避免解析循环和异常。</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">TXT最大长度</label>
                            <div class="col-sm-9">
                                <input type="number" name="record[txt_max_len]" class="form-control" placeholder="输入最大长度"
                                       value="{{ function_exists('safe_config_output') ? safe_config_output('sys.record.txt_max_len',255) : (is_array($v = config('sys.record.txt_max_len', 255)) ? 255 : $v) }}">
                                <div class="input_tips">单条 TXT 内容允许的最大长度，过长会影响兼容性与不同平台的解析限制。</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">TXT仅ASCII</label>
                            <div class="col-sm-9">
                                <select name="record[txt_ascii_only]" class="form-control">
                                    <option value="0" @if(config('sys.record.txt_ascii_only',1)==0) selected @endif>关闭</option>
                                    <option value="1" @if(config('sys.record.txt_ascii_only',1)==1) selected @endif>开启</option>
                                </select>
                                <div class="input_tips">开启后，TXT 记录仅允许英文、数字和常见符号，禁止中文等非 ASCII 字符，保证跨平台兼容性。</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('record')">保存</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    邮箱配置
                </div>
                <div class="card-body">
                    <form id="form-mail">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">SMTP服务器地址(host)</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[host]" class="form-control" placeholder="SMTP服务器地址"
                                       value="{{ function_exists('safe_config_output') ? safe_config_output('sys.mail.host','smtp.qq.com') : (is_scalar(config('sys.mail.host','smtp.qq.com')) ? config('sys.mail.host','smtp.qq.com') : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">SMTP服务器端口(port)</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[port]" class="form-control" placeholder="SMTP服务器端口"
                                       value="{{ function_exists('safe_config_output') ? safe_config_output('sys.mail.port','465') : (is_scalar(config('sys.mail.port','465')) ? config('sys.mail.port','465') : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">加密类型</label>
                            <div class="col-sm-9">
                                <select name="mail[encryption]" :value="'{{ function_exists('safe_config_output') ? safe_config_output('sys.mail.encryption','ssl') : (is_scalar(config('sys.mail.encryption','ssl')) ? config('sys.mail.encryption','ssl') : '') }}'"
                                        class="form-control">
                                    <option value="ssl">SSL</option>
                                    <option value="tls">TSL</option>
                                    <option value="">不加密</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">邮箱账号</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[username]" class="form-control" placeholder="邮箱账号"
                                       value="{{ function_exists('safe_config_output') ? safe_config_output('sys.mail.username') : (is_scalar(config('sys.mail.username')) ? config('sys.mail.username') : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">邮箱密码</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[password]" class="form-control" placeholder="邮箱密码"
                                       value="{{ function_exists('safe_config_output') ? safe_config_output('sys.mail.password') : (is_scalar(config('sys.mail.password')) ? config('sys.mail.password') : '') }}">
                                <div class="input_tips">这个密码可能不是邮箱登录密码，需要在邮箱里单独获取或者设置</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">发送测试</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[test]" class="form-control" placeholder="输入一个邮箱地址"
                                       value="{{ function_exists('safe_config_output') ? safe_config_output('sys.mail.test','123456@qq.com') : (is_scalar(config('sys.mail.test','123456@qq.com')) ? config('sys.mail.test','123456@qq.com') : '') }}">
                                <div class="input_tips">输入一个邮箱地址，用于测试发送邮件！</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('mail')">保存</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    域名配置
                </div>
                <div class="card-body">
                    <form id="form-domain">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">保留前缀</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="reserve_domain_name" placeholder="输入你想保留的域名前缀"
                                          rows="3"
                                >@php($__reserve = config('sys.reserve_domain_name')){{ is_array($__reserve) ? implode(',', $__reserve) : $__reserve }}</textarea>
                                <div class="input_tips">多个用,隔开 举例：www,m,3g,4g</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('domain')">保存</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    权限配置
                </div>
                <div class="card-body">
                    <form id="form-user_perm">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">允许修改邮箱</label>
                            <div class="col-sm-9">
                                <select name="user_perm[edit_email]" class="form-control">
                                    <option value="0" @if(config('sys.user_perm.edit_email',0)==0) selected @endif>关闭</option>
                                    <option value="1" @if(config('sys.user_perm.edit_email',0)==1) selected @endif>开启</option>
                                </select>
                                <div class="input_tips">开启后，用户可修改邮箱，状态将变为待认证并需重新认证。</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">记录操作权限</label>
                            <div class="col-sm-9">
                                <div class="form-inline">
                                    <label class="mr-2">添加</label>
                                    <select name="user_perm[add]" class="form-control mr-3" style="width:100px">
                                        <option value="0" @if(config('sys.user_perm.add',1)==0) selected @endif>否</option>
                                        <option value="1" @if(config('sys.user_perm.add',1)==1) selected @endif>是</option>
                                    </select>
                                    <label class="mr-2">修改</label>
                                    <select name="user_perm[update]" class="form-control mr-3" style="width:100px">
                                        <option value="0" @if(config('sys.user_perm.update',1)==0) selected @endif>否</option>
                                        <option value="1" @if(config('sys.user_perm.update',1)==1) selected @endif>是</option>
                                    </select>
                                    <label class="mr-2">删除</label>
                                    <select name="user_perm[delete]" class="form-control" style="width:100px">
                                        <option value="0" @if(config('sys.user_perm.delete',1)==0) selected @endif>否</option>
                                        <option value="1" @if(config('sys.user_perm.delete',1)==1) selected @endif>是</option>
                                    </select>
                                </div>
                                <div class="input_tips">关闭对应权限后，用户端将隐藏相关入口，后端也会拦截。</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">允许记录类型</label>
                            <div class="col-sm-9">
                                @php($types = config('sys.user_perm.types', ['A','AAAA','CNAME','MX','TXT']))
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="user_perm[types][]" value="A" @if(in_array('A',$types)) checked @endif>
                                    <label class="form-check-label">A</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="user_perm[types][]" value="AAAA" @if(in_array('AAAA',$types)) checked @endif>
                                    <label class="form-check-label">AAAA</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="user_perm[types][]" value="CNAME" @if(in_array('CNAME',$types)) checked @endif>
                                    <label class="form-check-label">CNAME</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="user_perm[types][]" value="MX" @if(in_array('MX',$types)) checked @endif>
                                    <label class="form-check-label">MX</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="user_perm[types][]" value="TXT" @if(in_array('TXT',$types)) checked @endif>
                                    <label class="form-check-label">TXT</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="user_perm[types][]" value="NS" @if(in_array('NS',$types)) checked @endif>
                                    <label class="form-check-label">NS</label>
                                </div>
                                <div class="input_tips">未勾选则在用户端不展示，后端亦不允许提交。</div>
                                <div class="input_tips">类型说明：A为IPv4地址；AAAA为IPv6地址；CNAME为别名指向其他域名；MX为邮件交换记录；TXT为文本记录（如SPF/DKIM/验证）；NS用于子域委派到其他权威DNS，不允许在根域设置。</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('user_perm')">保存</a>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('foot')
    <script>
        new Vue({
            el: '#vue',
            data: {},
            methods: {
                form: function (id) {
                    var vm = this;
                    this.$post("/admin/config", $("#form-" + id).serialize())
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.$message(data.message, 'success');
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        });
                },
                loadEpayReco: function(){
                    var box = document.getElementById('epay_reco');
                    if(!box) return;
                    var xhr = new XMLHttpRequest();
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
                            }else{
                                box.textContent='无法获取推荐';
                            }
                        }
                    };
                    xhr.send();
                }
            },
            mounted: function () {
                this.loadEpayReco();
            }
        });
    </script>
@endsection