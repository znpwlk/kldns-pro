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
                                       value="{{ config('sys.web.name') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">首页标题</label>
                            <div class="col-sm-9">
                                <input type="text" name="web[title]" class="form-control" placeholder="输入首页标题"
                                       value="{{ config('sys.web.title') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">网站关键词</label>
                            <div class="col-sm-9">
                                <input type="text" name="web[keywords]" class="form-control" placeholder="输入网站关键词"
                                       value="{{ config('sys.web.keywords') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">网站描述</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="web[description]" placeholder="输入网站描述"
                                >{{ config('sys.web.description') }}</textarea>
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
                                       value="{{ config('sys.user.point',0) }}">
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
                                       value="{{ config('sys.record.name_max_len',63) }}">
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
                                       value="{{ config('sys.record.txt_max_len',255) }}">
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
                                       value="{{ config('sys.mail.host','smtp.qq.com') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">SMTP服务器端口(port)</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[port]" class="form-control" placeholder="SMTP服务器端口"
                                       value="{{ config('sys.mail.port','465') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">加密类型</label>
                            <div class="col-sm-9">
                                <select name="mail[encryption]" :value="'{{ config('sys.mail.encryption','ssl') }}'"
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
                                       value="{{ config('sys.mail.username') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">邮箱密码</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[password]" class="form-control" placeholder="邮箱密码"
                                       value="{{ config('sys.mail.password') }}">
                                <div class="input_tips">这个密码可能不是邮箱登录密码，需要在邮箱里单独获取或者设置</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">发送测试</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[test]" class="form-control" placeholder="输入一个邮箱地址"
                                       value="{{ config('sys.mail.test','123456@qq.com') }}">
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
                                >{{ config('sys.reserve_domain_name') }}</textarea>
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
            },
            mounted: function () {
            }
        });
    </script>
@endsection