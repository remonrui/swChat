<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/27
 * Time: 11:38
 */

namespace app\server\controller;

use chat\Client;
use think\Exception;
use think\facade\Log;
use tools\Redis;

class WebSocketHandle
{
    protected static $redis;
    protected static $userInfo = "UserInfo";
    protected static $userStatus = "UserStatus";
    protected static $userOnline = "UserOnline";
    protected  static $on_line_count = 0;

    public static function redisInit()
    {
        self::$redis = new Redis();
        self::$redis->delete(self::$userStatus);
        self::$redis->delete(self::$userOnline);
    }

    public static function onOpenHandle($serv, $frame)
    {
        self::$on_line_count +=1;

        $serv->push($frame->fd,Client::send(100,'ok',['icon'=>'','fd'=>$frame->fd,'online'=>self::$on_line_count]));
        foreach($serv->connections as $fd) {
            if($fd != $frame->fd) {
                $serv->push($fd,Client::send(20,'ok',[
                    'online'=> self::$on_line_count,
                    'message'=> '',
                ]));
            }
        }

    }

    public  static function onMessageHandle($server, $frame)
    {
        try {
            if (!empty($frame) && $frame->opcode == 1) {
                $data = self::checkMessage($frame->data);
                $code  = isset($data['code']) ? $data['code'] : 0;
                switch ($code) {
                    case 10:
                        self::login($server,$data,$frame->fd);
                        break;
                    case 0:
                        self::chat($server,$data,$frame->fd);
                        break;
                    default;
                        break;

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

    public static function onCloseHandle($server,$fd)
    {

        $sessid = self::$redis->hGet(self::$userOnline,(string)$fd);

        if (!$sessid){
            return;
        }

        $userStr = self::$redis->hGet(self::$userInfo,$sessid);

        $user    = json_decode($userStr,true);
        self::$on_line_count -=1;
        if(!empty($user)) {
            foreach($server->connections as $_fd) {
                if($fd != $_fd) {
                    $server->push($_fd,Client::send(20,'ok',[
                        'online'=> self::$on_line_count,
                        'message'   => date("Y-m-d H:i")." <span style='font-weight: bolder;color: #008bff'>{$user['nick']}</span> 骚年下线了",
                    ]));
                }
            }
        }

        self::$redis->hDel(self::$userOnline,(string)$fd);

        self::$redis->hSet(self::$userStatus,$sessid,"0");

    }

    protected static function checkMessage($message)
    {
        $message = json_decode($message);
        $return_message = [];
        if (!is_array($message) && !is_object($message)) {
            return false;
        }
        if (is_object($message)) {
            foreach ($message as $item => $value) {
                $return_message[$item] = $value;
            }
        } else {
            $return_message = $message;
        }
        if (!isset($return_message["code"]) || !isset($return_message["message"])) {
            return false;
        } else {
            return $return_message;
        }
    }

    protected static function login($server, $data = [], $fd)
    {
        $userSessid = $data['sessid'];

        if (self::$redis->hGet(self::$userStatus,$userSessid) == "1")
        {
            $server->push($fd,Client::send(1,'ok',[
                'message'  =>'不要重复登录',
                'icon' =>"http://pics.sc.chinaz.com/Files/pic/icons128/5938/i6.png",
                'fd'   =>$fd,
                'time'=>date("H:i:s")
            ]));
            sleep(3);
            $server->close($fd);
            return;
        }else{
            $user = [
                'nick'=>$data['nick'],
                'icon'=>$data['icon'],
            ];
            if (!self::$redis->hExists(self::$userInfo,$userSessid)){
                self::$redis->hSet(self::$userInfo,$userSessid,json_encode($user));
            }

            self::$redis->hSet(self::$userOnline,(string)$fd,$userSessid);
            self::$redis->hSet(self::$userStatus,$userSessid,"1");

            foreach($server->connections as $fd) {
                $server->push($fd,Client::send($data['code'],'ok',[
                    'message'=> date("Y-m-d H:i")." <span style='font-weight: bolder;color: #ff0000'>{$user['nick']}</span> 骚年上线",
                ]));
            }
        }

    }

    protected static function chat($server, $data = [],$framd)
    {
        $userSessid = $data['sessid'];

        $userStr = self::$redis->hGet(self::$userInfo,$userSessid);

        $user = json_decode($userStr,true);

        foreach($server->connections as $fd) {
            $server->push($fd,Client::send(1,'ok',[
                'message'=> $data['message'],
                'code' => $data['code'],
                'icon'=>$user['icon'],
                'nick'=>$user['nick'],
                'time'=>date("H:i:s"),
                'fd'=>$framd,
            ]));
        }
    }
}