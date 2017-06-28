<?php
namespace App\Model;

use Illuminate\Database\Capsule\Manager as DB;

class Config extends \Illuminate\Database\Eloquent\Model
{
    protected $connection = 'default';
    protected $table = 'config';
    protected $guarded = [];

    public static function getConfByType($type = 1)
    {
        return DB::table('config')->where('type', '=', $type)->orderBy('id', 'desc')->first();
    }
}
