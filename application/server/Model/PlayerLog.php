<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/26
 * Time: 15:03
 */

namespace app\server\Model;


use think\Model;

class PlayerLog extends Model
{
    public function insertsAll($time,$msg,$status=0)
    {
        $data = json_decode($msg, true);
        $msg = isset($data['msg']) ? $data['msg'] : "";
        $fd = isset($data['fd']) ? $data['fd'] : 0;
        $ip = isset($data['ip']) ? $data['ip'] : 0;

        return self::execute(
            'insert into t_msg (fd,msg, add_time,status,ip) values (:fd, :msg, :add_time, :status,:ip)',
            [
                'fd' => $fd,
                'msg' => $msg,
                'add_time' => intval($time),
                'status' => intval($status),
                'ip' => $ip,
            ]
        );
    }
}