<?php

return [
    'rpcObj' => [
        'home-api' => '\App\servant\PHPTest\hyperf\rpcObj\TestTafServiceServant',
        'home-class' => '\App\servant\RpcImpl',
        'protocolName' => 'tars', //http, json, tars or other
        'serverType' => 'tcp', //http(no_tars default), websocket, tcp(tars default), udp
    ],
];
