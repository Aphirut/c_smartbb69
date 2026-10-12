<?php

require_once __DIR__ . '/vendor/autoload.php';

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Exception\UnknownEventTypeException;
use LINE\LINEBot\Exception\UnknownMessageTypeException;
use LINE\LINEBot\Exception\HttpRequestException;

// Channel Access Token และ Channel Secret
$channelAccessToken = 'JWstSz824hsyBTgmJcUWusUJbSFz1rO40aX1qXtb6mPKVcdYvQt8MN5BtOllaNrNG0sytLCIgwK4l20JqZw6wQymxR/vaSV9Xua6l7AX2mqoZomo/EvojK3BhmnKuMknuQa9KNejrReDinO2OZhviQdB04t89/1O/w1cDnyilFU='; // แทนที่ด้วย Access Token ของคุณ
$channelSecret = 'b46ee3b7cadae7732f297e02fdc4ca66'; // แทนที่ด้วย Channel Secret ของคุณ

$httpClient = new CurlHTTPClient($channelAccessToken);
$bot = new LINEBot($httpClient, ['channelSecret' => $channelSecret]);

$body = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

// ตรวจสอบลายเซ็น
try {
    $events = $bot->parseEventRequest($body, $signature);
} catch(InvalidSignatureException $e) {
    error_log('Invalid signature. Please check your channel access token/channel secret.');
    http_response_code(400);
    exit();
}

foreach ($events as $event) {
    if ($event instanceof \LINE\LINEBot\Event\MessageEvent) {
        if ($event->getMessage() instanceof \LINE\LINEBot\Message\TextMessage) {
            $replyToken = $event->getReplyToken();
            $messageText = $event->getMessage()->getText();

            // สร้างข้อความตอบกลับ
            $replyMessage = new TextMessageBuilder("คุณส่งข้อความว่า: " . $messageText);

            // ตอบกลับไปยังผู้ใช้
            try {
                $bot->replyMessage($replyToken, $replyMessage);
            } catch (HttpRequestException $e) {
                error_log("Request failed: " . $e->getMessage());
            }

        } else if ($event->getMessage() instanceof \LINE\LINEBot\Message\ImageMessage) {
            $replyToken = $event->getReplyToken();
            $message = new TextMessageBuilder("ได้รับรูปภาพแล้ว");
            $bot->replyMessage($replyToken, $message);
        } else if ($event->getMessage() instanceof \LINE\LINEBot\Message\VideoMessage) {
            $replyToken = $event->getReplyToken();
            $message = new TextMessageBuilder("ได้รับวิดีโอแล้ว");
            $bot->replyMessage($replyToken, $message);
        } else if ($event->getMessage() instanceof \LINE\LINEBot\Message\AudioMessage) {
            $replyToken = $event->getReplyToken();
            $message = new TextMessageBuilder("ได้รับเสียงแล้ว");
            $bot->replyMessage($replyToken, $message);
        } else if ($event->getMessage() instanceof \LINE\LINEBot\Message\LocationMessage) {
            $replyToken = $event->getReplyToken();
            $message = new TextMessageBuilder("ได้รับตำแหน่งแล้ว");
            $bot->replyMessage($replyToken, $message);
        } else if ($event->getMessage() instanceof \LINE\LINEBot\Message\StickerMessage) {
            $replyToken = $event->getReplyToken();
            $message = new TextMessageBuilder("ได้รับสติกเกอร์แล้ว");
            $bot->replyMessage($replyToken, $message);
        }
    } else if ($event instanceof \LINE\LINEBot\Event\FollowEvent) {
        $replyToken = $event->getReplyToken();
        $message = new TextMessageBuilder("สวัสดีครับ/ค่ะ ยินดีต้อนรับสู่บอทของเรา");
        $bot->replyMessage($replyToken, $message);
    }
}

?>