<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2019/4/16
 * Time: 11:24
 */

namespace App\Klsf\Dns;


use GuzzleHttp\Client;

class Cloudflare implements DnsInterface
{
    use DnsHttp;
    private $url = "https://api.cloudflare.com/client/v4/";
    private $apiKey;
    private $email;
    // 新增：zone 名称缓存，减少重复请求
    private $zoneNameCache = [];

    function deleteDomainRecord($RecordId, $DomainId, $Domain)
    {
        list($ret, $error) = $this->getResult("zones/{$DomainId}/dns_records/{$RecordId}", [], 'DELETE');
        return $ret ? [true, null] : [false, $error];
    }

    function updateDomainRecord($RecordId, $Name, $Type, $Value, $LineId, $DomainId = null, $Domain = null)
    {
        $params = [];
        $params['name'] = $Name;
        $params['type'] = $Type;
        $params['content'] = $Value;
        $params['proxied'] = $LineId ? true : false;
        list($ret, $error) = $this->getResult("zones/{$DomainId}/dns_records/{$RecordId}", $params, 'PUT');
        return $ret ? [true, null] : [false, $error];
    }

    function addDomainRecord($Name, $Type, $Value, $LineId, $DomainId = null, $Domain = null)
    {
        $params = [];
        $params['name'] = $Name;
        $params['type'] = $Type;
        $params['content'] = $Value;
        $params['proxied'] = $LineId ? true : false;
        list($ret, $error) = $this->getResult("zones/{$DomainId}/dns_records", $params, 'POST');
        if (!$ret) return [false, $error];
        if (isset($ret['result']['id'])) {
            $record = $ret['result'];
            $domainName = isset($record['zone_name']) ? $record['zone_name'] : $this->resolveZoneName($DomainId, $Domain);
            return [[
                'RecordId' => $record['id'],
                'Name' => $record['name'],
                'Domain' => $domainName
            ], null];
        }
        return [false, '添加域名记录失败'];
    }

    function getDomainRecordInfo($RecordId, $DomainId = null, $Domain = null)
    {
        list($ret, $error) = $this->getResult("zones/{$DomainId}/dns_records/{$RecordId}");
        if (!$ret) return [false, $error];
        if (isset($ret['result']['id'])) {
            $record = $ret['result'];
            $domainName = isset($record['zone_name']) ? $record['zone_name'] : $this->resolveZoneName($DomainId, $Domain);
            return [[
                'RecordId' => $record['id'],
                'Name' => $record['name'],
                'Type' => $record['type'],
                'Value' => $record['content'],
                'Domain' => $domainName
            ], null];
        }
        return [false, '获取域名记录详情失败'];
    }

    function getDomainRecords($DomainId = null, $Domain = null)
    {
        list($ret, $error) = $this->getResult("zones/{$DomainId}/dns_records");
        if (!$ret) return [false, $error];

        if (isset($ret['result'])) {
            $list = [];
            foreach ($ret['result'] as $record) {
                $domainName = isset($record['zone_name']) ? $record['zone_name'] : $this->resolveZoneName($DomainId, $Domain);
                $list[] = [
                    'RecordId' => $record['id'],
                    'Name' => $record['name'],
                    'Type' => $record['type'],
                    'Value' => $record['content'],
                    'Domain' => $domainName
                ];
            }
            return [$list, null];
        }
        return [false, '获取域名记录列表失败'];
    }

    function getDomainList()
    {
        list($ret, $error) = $this->getResult("zones?page=1&per_page=50");
        if (!$ret) return [false, $error];

        if (isset($ret['result'])) {
            $list = [];
            foreach ($ret['result'] as $domain) {
                $list[] = [
                    'Domain' => $domain['name'],
                    'DomainId' => $domain['id']
                ];
            }
            return [$list, null];
        }
        return [false, '获取域名列表失败'];
    }

    function getRecordLine($_domainId = null, $_domain = null)
    {
        $list = [];
        $list[] = array(
            'Name' => '默认',
            'Id' => 0,
        );
        $list[] = array(
            'Name' => 'CDN',
            'Id' => 1,
        );
        return $list;
    }

    function check()
    {
        list($ret, $error) = $this->getDomainList();
        return $ret ? [true, null] : [false, $error];
    }

    function config(array $config)
    {
        $this->email = isset($config['Email']) ? $config['Email'] : null;
        $this->apiKey = isset($config['ApiKey']) ? $config['ApiKey'] : null;

        $this->client = new Client([
            'timeout' => 30,
            'http_errors' => false,
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Auth-Email' => $this->email,
                'X-Auth-Key' => $this->apiKey
            ]
        ]);
    }

    function configInfo()
    {
        return [
            [
                'name' => 'ApiKey',
                'placeholder' => '请输入API Key',
                'tips' => 'API key generated on the "My Account" page'
            ],
            [
                'name' => 'Email',
                'placeholder' => '请输入Email',
                'tips' => 'Email address associated with your account'
            ]
        ];
    }

    // 新增：通过 ZoneId 反查 zone 名称（带内存缓存），当 $Domain 为空或接口返回缺少 zone_name 时使用
    private function resolveZoneName($DomainId, $Domain = null)
    {
        if (!empty($Domain)) return $Domain;
        if (empty($DomainId)) return null;
        if (isset($this->zoneNameCache[$DomainId])) {
            return $this->zoneNameCache[$DomainId];
        }
        list($ret, $error) = $this->getResult("zones/{$DomainId}");
        if ($ret && isset($ret['result']['name'])) {
            $this->zoneNameCache[$DomainId] = $ret['result']['name'];
            return $this->zoneNameCache[$DomainId];
        }
        return null;
    }

    private function getResult($action, $params = [], $method = 'GET')
    {
        list($res, $error) = $this->request($method, $this->url . $action, [
            'body' => json_encode($params)
        ]);

        if (!$res) return [false, $error];

        $body = (string)$res->getBody();
        if ($ret = json_decode($body, true)) {
            if (isset($ret['success'])) {
                if ($ret['success']) {
                    return [$ret, null];
                } elseif (isset($ret['errors']) && count($ret['errors']) > 0) {
                    return [false, $ret['errors'][0]['message']];
                }
            }
        }
        return [false, '解析结果失败'];
    }
}
?>