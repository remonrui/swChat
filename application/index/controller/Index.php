<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/26
 * Time: 14:15
 */

namespace app\index\controller;


use think\Controller;
use chat\Client;
use app\server\Model\asyncTask;

class Index extends Controller
{
    public function index()
    {
        Client::setCookie();
        $msg = json_encode([
            'msg'=>"【用户登陆】|CLASS:".__CLASS__."|Func:".__FUNCTION__,
            'fd'=>0,
            'ip'=> ip()
        ]);
        asyncTask::LogToDb(null, null, null, $msg);

        return $this->fetch();
    }
}