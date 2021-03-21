<?php

return array(
    'appName' => 'PHPTest',
    'serverName' => 'hyperf',
    'objName' => 'rpcObj',
    'withServant' => true,//决定是服务端,还是客户端的自动生成
    'tarsFiles' => array(
        './example.tars'
    ),
    'dstPath' => '../src/app/servant',//这里用来autoload
    'namespacePrefix' => 'App\servant',
);