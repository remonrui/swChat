SwChat
===============

原本打算使用swoft框架，但发现swoft的websocket有些Bug(ps:这个后说)，故使用TP5.1框架。

> 运行此项目你需要：

## 下载

git clone ---

## 安装

使用composer安装

~~~
composer install
~~~

启动服务

~~~
cd swChat
php think swoole:socket
~~~

然后就可以在浏览器中访问

~~~
http://localhost
~~~