<?php

namespace App\servant;

use App\servant\PHPTest\hyperf\rpcObj\TestTafServiceServant;

class RpcImpl implements TestTafServiceServant
{
    /**
     * @param string $name
     * @param string $outGreetings =out=
     */
    public function sayHelloWorld($name, &$outGreetings)
    {
        $outGreetings = 'hello world!';
    }

}