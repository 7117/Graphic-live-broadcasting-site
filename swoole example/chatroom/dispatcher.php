<?php

/**
 * 用于实现公聊私聊的特定发送服务。
 * */
class Dispatcher
{

    const CHAT_TYPE_PUBLIC = "publicchat";
    const CHAT_TYPE_PRIVATE = "privatechat";

    public $frame = '';

    public $clientid = '';

    public $chatData = '';

    public function __construct($frame)
    {
        $this->frame = $frame;
        $this->clientid = intval($this->frame->fd);
        print_r($frame);
    }

    public function getChatData()
    {
        $frameData = $this->frame->data;

        if ($frameData) {
            $frameData = json_decode($frameData, true);
            $this->chatData = $frameData;
            return $this->chatData;
        }
    }

    public function getSenderId()
    {
        return $this->clientid;
    }

    public function getReceiverId()
    {
        return intval($this->chatData['chatto']);
    }

    public function isPrivateChat()
    {
        $chatdata = $this->getChatData();
        return $chatdata['chattype'] == self::CHAT_TYPE_PUBLIC ? false : true;
    }

    public function sendPrivateChat($server, $toid, $msg)
    {

        foreach ($server->connections as $key => $fd) {
            if ($toid == $fd || $this->clientid == $fd) {
                $info = [
                    'msg' => $msg,
                ];
                $server->push($fd, json_encode($info));
            }
        }

        return;
    }

    public function sendToEvery($server, $msg)
    {

        $total = $server->getClientList();

        foreach ($server->connections as $key => $fd) {
            $info = [
                'msg' => $msg,
                'total' => $total
            ];

            $server->push($fd, json_encode($info));
        }

        return;

    }
}
