<?php
declare(strict_types=1);

namespace App\Tars;

use \Tars\report\ServerFSync;
use \Tars\report\ServerInfo;
use \Tars\Utils;

/**
 *
 */
class Manage
{

    public function getNodeInfo(){
        $conf = $this->getTarsConf();
        if( !empty($conf) ){
            $node = $conf['tars']['application']['server']['node'];
            $nodeInfo = Utils::parseNodeInfo($node);
            return $nodeInfo;
        }else{
            return [];
        }
    }

    public function getAdapterObjs(){
        $adapterObjs = [];
        $conf = $this->getTarsConf();
        $adapters = $conf['tars']['application']['server']['adapters'];
        foreach ( $adapters as $adapter ){
            $adapterObjs[] = $adapter['adapterName'];
        }
        return $adapterObjs;
    }

    public function getTarsConf(){
        $tars_conf = dirname(BASE_PATH,2).'/conf/'.env('APP_NAME').'.config.conf';

        if( is_file($tars_conf) ){
            $conf = Utils::parseFile($tars_conf);
            return $conf;
        }else{
            var_dump('get tars_conf file error : '.$tars_conf);
            return [];
        }
    }

    public function keepAlive()
    {
        $nodeInfo = $this->getNodeInfo();
        if( empty($nodeInfo) ){
            var_dump('keepAlive getNodeInfo fail');
            return null;
        }
        $host = $nodeInfo['host'];
        $port = $nodeInfo['port'];
        $objName = $nodeInfo['objName'];

        $pname = env('APP_NAME');
        $pname = explode('.',$pname);
        $application = $pname[0];
        $serverName = $pname[1];
        $masterPid = getmypid();

        $adapterObjs = $this->getAdapterObjs();
        $adapterObjs[] = 'AdminAdapter';
        foreach ($adapterObjs as $adapter){
            $serverInfo = new ServerInfo();
            $serverInfo->adapter = $adapter;
            $serverInfo->application = $application;
            $serverInfo->serverName = $serverName;
            $serverInfo->pid = $masterPid;

            $serverF = new ServerFSync($host, $port,$objName);
            $serverF->keepAlive($serverInfo);
        }

        var_dump(' keepalive ');
    }
}