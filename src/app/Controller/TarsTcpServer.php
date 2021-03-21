<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Contract\OnReceiveInterface;
use Tars\App;
use Tars\Code;
use Tars\core\Request;
use Tars\core\Response;
use Tars\core\TarsPlatform;
use Tars\protocol\ProtocolFactory;
use Tars\protocol\TARSProtocol;

class TarsTcpServer implements OnReceiveInterface
{

    protected static $paramInfos;
    protected static $impl;

    /**
     * TarsTcpServer constructor.
     */
    public function __construct()
    {
        $servicesInfo = require_once BASE_PATH . '/services.php';
        foreach ( $servicesInfo as $objName=>$obj ){
            if( $obj['protocolName']=='tars' ){
                $protocol = new TARSProtocol();
                $className = $servicesInfo[$objName]['home-class'];
                self::$impl[$objName] = new $className();
                $interface = new \ReflectionClass($servicesInfo[$objName]['home-api']);
                $methods = $interface->getMethods();

                foreach ($methods as $method) {
                    $docBlock = $method->getDocComment();
                    // 对于注释也应该有自己的定义和解析的方式
                    self::$paramInfos[$objName][$method->name] = $protocol->parseAnnotation($docBlock);
                }
            }
        }
        var_dump('TarsTcpServer init');
    }


    /**
     * @param \Swoole\Server $server
     * @param int $fd
     * @param int $fromId
     * @param string $data
     */
    public function onReceive($server, int $fd, int $fromId, string $data): void
    {
        $resp = new Response();
        $resp->fd = $fd;
        $resp->fromFd = $fromId;
        $resp->server = $server;

        // 处理管理端口的特殊逻辑
        $unpackResult = \TUPAPI::decodeReqPacket($data);
        $sServantName = $unpackResult['sServantName'];
        $sFuncName = $unpackResult['sFuncName'];
        $objName = explode('.', $sServantName)[2];

        //if (!isset(self::$paramInfos[$objName]) || !isset(self::$impl[$objName])) {
        //    App::getLogger()->error(__METHOD__ . " objName $objName not found.");
        //    $resp->send('');
        //    //TODO 这里好像可以直接返回一个taf error code 提示obj 不存在的
        //    return;
        //}

        $req = new Request();
        $req->reqBuf = $data;
        $req->paramInfos = self::$paramInfos[$objName];
        $req->impl = self::$impl[$objName];

        // 处理管理端口相关的逻辑
        //if ($sServantName === 'AdminObj') {
        //    TarsPlatform::processAdmin($this->tarsConfig, $unpackResult, $sFuncName, $resp, $this->sw->master_pid);
        //}

        $impl = $req->impl;
        $paramInfos = $req->paramInfos;
        $protocol = new TARSProtocol();

        try {
            // 这里通过protocol先进行unpack
            $result = $protocol->route($req, $resp, []);
            if (is_null($result)) {
                return;
            } else {
                $sFuncName = $result['sFuncName'];
                $args = $result['args'];
                $unpackResult = $result['unpackResult'];
                if (method_exists($impl, $sFuncName)) {
                    $returnVal = $impl->$sFuncName(...$args);
                } else {
                    throw new \Exception(Code::TARSSERVERNOFUNCERR);
                }
                $paramInfo = $paramInfos[$sFuncName];
                $rspBuf = $protocol->packRsp($paramInfo, $unpackResult, $args, $returnVal);
                $resp->send($rspBuf);
                return;
            }
        } catch (\Exception $e) {
            $unpackResult['iVersion'] = 1;
            $rspBuf = $protocol->packErrRsp($unpackResult, $e->getCode(), $e->getMessage());
            $resp->send($rspBuf);
            return;
        }
        //$server->send($fd, 'recv:' . $data);
    }
}