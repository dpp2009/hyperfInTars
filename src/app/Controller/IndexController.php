<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

class IndexController extends AbstractController
{
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        return [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }
    public function testRpc()
    {
        $config = new \Tars\client\CommunicatorConfig();
        $config->setLocator('tars.tarsregistry.QueryObj@tcp -h 172.25.0.3 -p 17890');
        $config->setModuleName('TestApp.HelloServer');
        $config->setCharsetName('UTF-8');
        $config->setSocketMode(2);

        return [];
    }
}
