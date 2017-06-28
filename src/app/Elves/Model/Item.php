<?php
namespace App\Model;

use Illuminate\Database\Capsule\Manager as DB;

class Item extends \Illuminate\Database\Eloquent\Model
{
    protected $connection = 'default';
    protected $table = 'item';
    protected $guarded = [];

    public static function getItems($opts = [])
    {
        $state = isset($opts['state']) ? (int)$opts['state'] : 1;
        $offset = isset($opts['offset']) ? (int)$opts['offset'] : 0;
        $size = isset($opts['size']) ? (int)$opts['size'] : 20;
        if ($state !== null) {
            $query = DB::table('item')->where('state', '=', $state);
        } else {
            $query = DB::table('item');
        }
        $kw = isset($opts['kw']) ? (string)$opts['kw'] : '';
        if (!empty($kw)) {
            $query->where(function($q) use ($kw) {
                $q->where('title', 'like', '%' . $kw . '%')
                    ->orWhere('brief', 'like', '%' . $kw . '%');
            });
        }

        return ['total' => $query->count(), 'result' => $query->orderBy('id', 'desc')->take($size)->skip($offset)->get()]; // DONOT MODIFY THE ORDER
    }

    public static function format($item, $getRel = true)
    {
        if ($item instanceof Item) {
            $ret = $item->toArray();
            unset($ret['created_at'],
                $ret['updated_at'],
                $ret['extra'],
                $ret['state'],
                $ret['orig_id'],
                $ret['source']
            );
            return $ret;
        }

        return $item;
    }
}
