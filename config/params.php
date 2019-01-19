<?php

return [
    'adminEmail' => 'admin@example.com',
    'AAC_PATH_PRE' => '/data/static/voice/story/aac/',
    'messageCode' => require 'message.php',
    'wechatConfig' => require 'wechat.php',
    'wechatMiniProgramConfig' => require 'mini_program.php',
    'storyRedisKey' => [
        'REDIS_PUBLISH_PATER' => 'REDIS_PUBLISH_PATER',
        'REDIS_USER_REPLY' => 'REDIS_USER_REPLY',
    ],
];
