<?php

define('JSON_ENCODE_OPT', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);

function jsonEncode($data)
{
    return json_encode($data, JSON_ENCODE_OPT);
}
function jsonDecode($data)
{
    return json_decode($data, true);
}
function mbTrunc($str, $len, $trail = '...', $enc = 'utf-8')
{
    return mb_strlen($str, $enc) >= $len ? mb_substr($str, 0, $len, $enc) . $trail : $str;
}

function preDump($var){
    echo '<pre>';
    var_dump($var);
}

function sqlBeginLog($connName = '')
{
    \Illuminate\Database\Capsule\Manager::connection($connName)->enableQueryLog();
}
function sqlDumpLog($connName = '')
{
    echo '<pre>';
    var_dump(\Illuminate\Database\Capsule\Manager::connection($connName)->getQueryLog());
}
function sqlBeginTrans($connName = '')
{
    \Illuminate\Database\Capsule\Manager::connection($connName)->beginTransaction();
}
function sqlCommit($connName = '')
{
    \Illuminate\Database\Capsule\Manager::connection($connName)->commit();
}
function sqlRollback($connName = '')
{
    \Illuminate\Database\Capsule\Manager::connection($connName)->rollback();
}
function sqlTrans(Closure $callback, $connName = '')
{
    return \Illuminate\Database\Capsule\Manager::connection($connName)->transaction($callback);
}
