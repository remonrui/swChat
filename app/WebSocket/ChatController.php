<?php
namespace App\WebSocket;
use App\Lib\DataFormat;
use App\Lib\MsgCode;
use Swoft\Helper\JsonHelper;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Message\Server\Response;
use Swoft\WebSocket\Server\Bean\Annotation\WebSocket;
use Swoft\WebSocket\Server\HandlerInterface;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
/**
 * Class EchoController
 * @package App\WebSocket
 * @WebSocket("/chat")
 */
class ChatController implements HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function checkHandshake(Request $request, Response $response): array
    {
        return [0, $response];
    }
    /**
     * @param Server $server
     * @param Request $request
     * @param int $fd
     */
    public function onOpen(Server $server, Request $request, int $fd)
    {
        $server->push($fd, 'hello, welcome! :)');
    }
    /**
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame)
    {
        //$server->push($frame->fd, 'hello, I have received your message: ' . $frame->data);

        $fd = $frame->fd;

        try{
            $data = JsonHelper::decode($frame->data);

            $userName = 'user' . $fd;




        }catch (\Exception $E){

            $server->push($fd,DataFormat::show(MsgCode::$paramsError));

        }
    }
    /**
     * @param Server $server
     * @param int $fd
     */
    public function onClose(Server $server, int $fd)
    {
        // do something. eg. record log, unbind user ...
    }
}
