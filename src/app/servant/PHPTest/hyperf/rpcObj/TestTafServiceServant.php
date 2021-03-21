<?php

namespace App\servant\PHPTest\hyperf\rpcObj;

interface TestTafServiceServant {
	/**
	 * @param string $name 
	 * @param string $outGreetings =out=
	 * @return void
	 */
	public function sayHelloWorld($name,&$outGreetings);
}

