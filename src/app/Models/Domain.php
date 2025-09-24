<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 2019/4/14
 * Time: 16:36
 */

namespace App\Models;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Domain extends Model
{
    protected $primaryKey = 'did';
    protected $guarded = ['did'];

    public function scopeAvailable($query, $gid = 0)
    {
        $gid = $gid ? $gid : (Auth::check() ? Auth::user()->gid : 0);
        $query->where(function ($q) use ($gid) {
            $q->where('groups', '0');
            if ($gid > 0) {
                $q->orWhereRaw("FIND_IN_SET(?, groups)", [$gid]);
            }
        });
    }

    public function dnsConfig()
    {
        return $this->belongsTo(DnsConfig::class, 'dns', 'dns');
    }
}