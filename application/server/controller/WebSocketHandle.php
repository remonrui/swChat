<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/27
 * Time: 11:38
 */

namespace app\server\controller;

use think\Exception;
use think\facade\Log;
use tools\Redis;

class WebSocketHandle
{
    protected static $redis;
    protected static $userSet = "UserOnlineSet";
    protected static $userHash = "UserHash";

    public static function redisInit()
    {
        self::$redis = new Redis();
        $userList = self::$redis->smembers(self::$userSet);
        //主程序启动 清空所有聊天室在线用户
        if (!empty($userList) ) {
            foreach ($userList as $user) {
                self::$redis->delete($user);
            }
        }
        //创建内存表
//        $this->createTable();
    }

    public static function onOpenHandle($server, $request)
    {
        var_dump($request->cookie);
        echo "server: handshake success with fd{$request->fd}\n";
    }

    public  function onMessageHandle($server, $frame)
    {
        try {
            if (!empty($frame) && $frame->opcode == 1 && $frame->finish == 1) {
                $message = self::checkMessage($frame->data);
                if (!$message) {
                    $this->serverPush($server, $frame->fd, $frame->data, 'message');
                }
                if (isset($message["type"])) {
                    switch ($message["type"]) {
                        case "login":
                            $this->login($server, $frame->fd, $message["message"], $message["room"]);
                            break;
                        case "message":
                           self::serverPush($server, $frame->fd, $message["message"], 'message');
                            break;
                        default:
                    }
                   self::$redis->sadd(self::$roomListName, $message["room"]);
                }
            } else {
                throw new Exception("接收数据不完整");
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    }

    public static function onRequestHandle()
    {

    }

    public static function onCloseHandle()
    {

    }

    protected static function serverPush( $server, $frame_fd, $message = "", $message_type = "message")
    {
        $message = htmlspecialchars($message);
        $datetime = date('Y-m-d H:i:s', time());
        $userList = self::$redis->smembers(self::$userSet);
        if (isset($userList)) {
            foreach ($userList as $fd) {
                if ($fd == $frame_fd) {
                    continue;
                }
                @$server->push($fd, json_encode([
                        'type' => $message_type,
                        'message' => $message,
                        'datetime' => $datetime,
                    ])
                );
            }
        }
    }

    protected static function checkMessage($message)
    {
        $message = json_decode($message);
        $return_message = [];
        if (!is_array($message) && !is_object($message)) {
            self::$error = "接收的message数据格式不正确";
            return false;
        }
        if (is_object($message)) {
            foreach ($message as $item => $value) {
                $return_message[$item] = $value;
            }
        } else {
            $return_message = $message;
        }
        if (!isset($return_message["sessid"]) || !isset($return_message["message"])) {
            return false;
        } else {
            return $return_message;
        }
    }

    protected static function login($server, $frame_fd, $message = "", $room)
    {

    }
}