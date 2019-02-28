<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Env;
use app\server\controller\WebSocketHandle;

// +----------------------------------------------------------------------
// | Swoole设置 php think swoole:server 命令行下有效
// +----------------------------------------------------------------------
return [
    // 扩展自身配置
    'host'         => '0.0.0.0', // 监听地址
    'port'         => 9508, // 监听端口
    'type'         => 'socket', // 服务类型 支持 socket http server
    'mode'         => '', // 运行模式 默认为SWOOLE_PROCESS
    'sock_type'    => '', // sock type 默认为SWOOLE_SOCK_TCP
    'swoole_class' => '', // 自定义服务类名称

    // 可以支持swoole的所有配置参数
    'daemonize'    => false,
    'pid_file'     => Env::get('runtime_path') . 'swoole_server.pid',
    'log_file'     => Env::get('runtime_path') . 'swoole_server.log',

    // 事件回调定义
    'onOpen'       => function ($server, $request) {

        WebSocketHandle::onOpenHandle($server, $request);

    },

    'onMessage' => function ($server, $frame) {

        WebSocketHandle::onMessageHandle($server, $frame);
    },

    'onRequest' => function ($request, $response) {

    },

    'onClose' => function ($server, $fd) {

        WebSocketHandle::onCloseHandle($server,$fd);
        echo "client {$fd} closed\n";
    },
    /**
     * 异步task
     */
    'onTask' =>function($ser, $task_id,$from_id, $data){

    },
];
