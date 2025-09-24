<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2019/4/14
 * Time: 17:37
 */

namespace App;

use App\Models\Domain;
use App\Models\DomainRecord;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

if (!function_exists('safe_config_output')) {
    /**
     * Safely outputs a configuration value, handling arrays or other non-scalar types.
     *
     * @param string $key
     * @param mixed $default
     * @return string
     */
    function safe_config_output($key, $default = '')
    {
        $value = config($key, $default);
        if (is_array($value)) {
            return implode(',', $value);
        }
        return is_scalar($value) ? $value : (string)$default;
    }
}

class Helper
{
//是否是Pjax请求
    public static function isPjax()
    {
        return request()->header('X-PJAX') === 'true';
    }

    //根据IP获取城市名称
    public static function getIpCity($ip = null)
    {
        $ip = $ip ? $ip : request()->getClientIp();
        $client = static::client();
        $res = $client->get("http://ip.ws.126.net/ipquery?ip={$ip}");
        if ($res->getStatusCode() === 200) {
            $body = (string)$res->getBody();
            $body = mb_convert_encoding($body, 'UTF-8', 'GBK');
            $body = explode('localAddress=', $body);
            if ($ret = json_decode(str_replace(['city', 'province'], ['"city"', '"province"'], $body[1]))) {
                return str_replace('省', '', $ret->province) . str_replace('市', '', $ret->city);
            }
        }
        return 'Unknown';
    }

    /**
     * @return Client
     */
    public static function client()
    {
        return new Client([
            'timeout' => 60,
            'http_errors' => false,
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8'
            ]
        ]);
    }

    //是否是手机访问
    public static function isMobile()
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }


    //获取可用域名列表
    public static function getAvailableDomains()
    {
        return Domain::available()->get();
    }

    //检查域名前缀是否可用
    public static function checkDomainName($name)
    {
        $name = strtolower(trim($name));
        $reserve = explode(',', config('sys.reserve_domain_name'));
        if (strlen($name) < 1) {
            return [false, '请输入域名前缀'];
        } elseif (!preg_match('/^[a-z0-9\_\-]+$/', $name)) {
            return [false, '域名前缀格式不正确'];
        } elseif (in_array($name, $reserve)) {
            return [false, '对不起，此前缀暂不对外开放'];
        } else {
            return [$name, null];
        }
    }

    //发送邮件
    public static function sendEmail($to, $subject, $view, $array = [])
    {
        // 基础邮箱配置校验与防御处理
        $sysMail = [
            'host' => config('sys.mail.host'),
            'port' => config('sys.mail.port'),
            'username' => config('sys.mail.username'),
            'password' => config('sys.mail.password'),
            'encryption' => config('sys.mail.encryption'),
        ];
        if (!$sysMail['host'] || !$sysMail['port'] || !$sysMail['username'] || !$sysMail['password']) {
            return [false, "未配置邮箱信息"];
        }
        if (!static::checkEmail($to)) {
            return [false, '测试邮箱地址格式不正确'];
        }
        // 兼容当 config('mail') 为 null 时的情况
        $mailConfig = config('mail', []);
        if (!is_array($mailConfig)) {
            $mailConfig = [];
        }
        // 设置必要字段，避免数组下标为 null 的错误
        $mailConfig['driver'] = $mailConfig['driver'] ?? 'smtp';
        $mailConfig['host'] = $sysMail['host'];
        $mailConfig['port'] = $sysMail['port'];
        $mailConfig['username'] = $sysMail['username'];
        $mailConfig['password'] = $sysMail['password'];
        $mailConfig['encryption'] = $sysMail['encryption'] ?? null;
        $mailConfig['from'] = [
            'address' => $sysMail['username'],
            'name' => config('sys.web.name', '二级域名分发')
        ];
        config(['mail' => $mailConfig]);

        try {
            Mail::send($view, $array, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $message = $message ? mb_convert_encoding($e->getMessage(), 'UTF-8') : '发送邮件出错！';
            return [false, $message];
        }
        if (count(Mail::failures()) < 1) {
            return [true, null];
        } else {
            return [false, Mail::failures()];
        }
    }


    //检查邮件格式是否正确
    public static function checkEmail($email)
    {
        return preg_match('/^[a-zA-Z0-9\.\-\_]+\@([a-zA-Z0-9\_\-]+\.)+[a-zA-Z]+$/i', $email);
    }

    //发送激活邮件
    public static function sendVerifyEmail(User $user)
    {
        $url = "http://{$_SERVER['HTTP_HOST']}/verify?code=" . Crypt::encrypt($user->sid);
        return static::sendEmail($user->email, '注册会员激活邮件', 'email.verify', [
            'username' => $user->username,
            'webName' => config('sys.web.name', 'app.name'),
            'url' => $url
        ]);
    }

    //删除解析记录
    public static function deleteRecord(DomainRecord $record)
    {
        if ($domain = $record->domain) {
            if ($dns = $domain->dnsConfig) {
                if ($_dns = \App\Klsf\Dns\Helper::getModel($dns->dns)) {
                    $_dns->config($dns->config);
                    list($ret, $error) = $_dns->deleteDomainRecord($record->record_id, $domain->domain_id, $domain->domain);
                    return $ret ? true : false;
                }
            }
        }
        return false;
    }

    //获取首页链接
    public static function getIndexUrls()
    {
        $list = [];
        $str = config('sys.index_urls');
        $rows = explode("
", $str);
        foreach ($rows as $row) {
            $row = explode('|', trim($row));
            if (count($row) == 2) {
                $list[] = $row;
            }
        }
        return $list;
    }
}