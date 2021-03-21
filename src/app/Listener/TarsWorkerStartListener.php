<?php

declare(strict_types=1);

namespace App\Listener;

use App\Tars\Manage;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeWorkerStart;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class TarsWorkerStartListener implements ListenerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            BeforeWorkerStart::class,
        ];
    }

    /**
     * @param QueryExecuted $event
     */
    public function process(object $event)
    {
        $workerId = $event->workerId;
        if( $workerId==0 ){

            $manage = new Manage();
            $manage->keepAlive();

            \Swoole\Timer::tick(30000, function(){
                $manage = new Manage();
                $manage->keepAlive();
                //var_dump(['WorkerStartListener Timer ']);
            });

        }
    }
}
