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


class Index extends Controller
{
    public function index()
    {
        Client::setCookie();

        return $this->fetch();
    }
}