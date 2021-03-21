<?php

$args = $_SERVER['argv'];

$tarsproto = require_once(dirname(__FILE__,2).'/tars/tars.proto.php');
$appName = $tarsproto['appName'];
$serverName = $tarsproto['serverName'];
$logFile = "/usr/local/app/tars/app_log/$appName/$serverName/$appName.$serverName.log";

$commend = $args[2];  //start stop restart
$commend = strtolower($commend);

$bin = dirname(__FILE__).'/bin/hyperf.php ';

if( $commend=='stop' ){
    stop("$appName.$serverName");
}elseif( $commend=='restart' ){
    stop("$appName.$serverName");
    start($bin,$logFile);
}else{
    start($bin,$logFile);
}

function start($bin,$logFile){
    $cmd = "/usr/bin/php -d swoole.use_shortname=Off " . $bin . "start > $logFile 2>&1 & ";
    exec($cmd, $output, $r);
}

function stop($name){
    $ret = getProcess($name);
    if ($ret['exist'] === false) {
        echo "{$name} stop  \033[34;40m [FAIL] \033[0m process not exists". PHP_EOL;
        return;
    }

    $pidList = implode(' ', $ret['pidList']);
    $cmd = "kill -9 {$pidList}";
    exec($cmd, $output, $r);

    if ($r === false) { // kill失败时
        echo "{$name} stop  \033[34;40m [FAIL] \033[0m posix exec fail"
            . PHP_EOL;
        exit;
    }
    echo "{$name} stop  \033[32;40m [SUCCESS] \033[0m" . PHP_EOL;
}

function getProcess($processName)
{
    $cmd = "ps aux | grep '" . $processName . "' | grep Master | grep -v grep  | awk '{ print $2}'";
    exec($cmd, $ret);

    $cmd = "ps aux | grep '" . $processName . "' | grep Manager | grep -v grep  | awk '{ print $2}'";
    exec($cmd, $ret);

    $cmd = "ps aux | grep '" . $processName . "' | grep Worker | grep -v grep  | awk '{ print $2}'";
    exec($cmd, $ret);

    $cmd = "ps aux | grep '" . $processName . "' | grep Task | grep -v grep  | awk '{ print $2}'";
    exec($cmd, $ret);

    if (empty($ret)) {
        return [
            'exist' => false,
        ];
    } else {
        return [
            'exist' => true,
            'pidList' => $ret,
        ];
    }
}


