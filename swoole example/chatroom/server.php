<?php

include "./dispatcher.php";

class Server
{
    public function __construct()
    {
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

        $ws = new swoole_websocket_server("0.0.0.0", 8811);

        //设置静态页
        $ws->set([
            'enable_static_handler' => true,
            'document_root' => "./",
            'worker_num' => 5
        ]);


        $ws->on("open", function ($ws, $request) {
            echo "open:client {$request->fd}" . PHP_EOL;

            $count = count($ws->connections);
            //获取所有的连接  进行遍历展示
            $totalConn = $ws->getClientList();

            foreach ($ws->connections as $key => $fd) {
                $welcomeWord = "";
                $info = [
                    'msg' => $welcomeWord,
                    'total' => $totalConn
                ];

                $ws->push($fd, json_encode($info));
            }

        });

        // $frame 是 swoole_websocket_frame 对象，包含了客户端发来的数据帧信息
        $ws->on("message", function ($ws, $frame) {
            //接收客户端的信息
            $dispatcher = new Dispatcher($frame);
            //获取
            $chatdata = $dispatcher->getChatData();
            $fromid = $dispatcher->getSenderId();
            $toid = $dispatcher->getReceiverId();
            $isprivatechat = $dispatcher->isPrivateChat();

            //私聊
            if ($isprivatechat) {
                $msg = "【{$fromid}】对【{$toid}】说：{$chatdata['chatmsg']}";
                $dispatcher->sendPrivateChat($ws, $toid, $msg);
                //公聊
            } else {
                $msg = "【{$fromid}】对大家说：{$chatdata['chatmsg']}";
                $dispatcher->sendToEvery($ws, $msg);
            }
        });

        $ws->on("close", function ($ws, $fd) {
            if ($ws->isEstablished) {
                foreach ($ws->connections as $key => $fd) {
                    $goodbyeMag = "CLOSE:Client {$fd} leave this chat room.";

                    $info = [
                        'msg' => $goodbyeMag,
                    ];

                    $ws->push($fd, json_encode($info));
                }
            }
        });

        $ws->start();


    }
}


$ws = new Server();


