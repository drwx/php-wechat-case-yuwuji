<?php
namespace App\Model;

use Illuminate\Database\Capsule\Manager as DB;

class Emotion extends \Illuminate\Database\Eloquent\Model
{
    protected $connection = 'default';
    protected $table = 'emotion';
    protected $guarded = [];
}
