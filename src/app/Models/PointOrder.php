<?php
namespace App\Models;

class PointOrder extends Model
{
    protected $table = 'point_orders';
    protected $fillable = ['order_no','uid','amount','point','status','pay_type','trade_no','notify_data','ip'];

    public static function genOrderNo(): string
    {
        return date('YmdHis') . mt_rand(100000, 999999);
    }
}