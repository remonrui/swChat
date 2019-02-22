<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 14:30
 */

namespace App\Lib;


use Swoft\Helper\JsonHelper;

class DataFormat
{
    public static function show($status,$fd=null,$msg=null)
    {
        $result = [
           'status'=>$status,
           'data'=>[
               'fd'=>$fd,
               'msg'=>$msg
           ],
        ];

        return JsonHelper::encode($result,JSON_UNESCAPED_UNICODE);
    }
}
