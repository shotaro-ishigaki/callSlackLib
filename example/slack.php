<?php

require_once dirname(__DIR__) . '/lib/class/SlackClass.php';

$chanel = '';
$slack_url = '';
$options = [];
$message = '通知テストです。';

// パターンA
$slack_class = new SlackClass(
    channel : $chanel,
    slack_url : $slack_url,
    options : $options
);

// パターンB
// $slack_class = new SlackClass();
// $slack_class->setChannel($chanel);
// $slack_class->setSlackUrl($slack_url);
// $slack_class->setOptions($options);

$slack_class->send($message);
