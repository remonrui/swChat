<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/27
 * Time: 11:09
 */

namespace app\server\command;

use think\swoole\command\Server;
use think\console\input\Argument;
use think\console\input\Option;
use app\server\controller\WebSocketHandle;

class WebSocket extends Server
{
    public function configure()
    {
        $this->setName('swoole:socket')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload", 'start')
            ->addOption('host', 'H', Option::VALUE_OPTIONAL, 'the host of swoole server.', null)
            ->addOption('port', 'p', Option::VALUE_OPTIONAL, 'the port of swoole server.', null)
            ->addOption('daemon', 'd', Option::VALUE_NONE, 'Run the swoole server in daemon mode.')
            ->setDescription('Swoole Server for ThinkPHP');
    }

    protected function init()
    {
       parent::init();

       //todo webSocket启动或重启时初始化

        WebSocketHandle::redisInit();
    }

}