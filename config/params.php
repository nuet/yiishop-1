<?php

return [
    'adminEmail' => 'admin@example.com',
    'pageSize' => [
        'manage' => 2,//后台管理员列表分页,每页显示条数
        'user'   => 1,//后台用户列表分页,每页显示条数
        'goods'   => 10,
        'order'   => 10,
    ],
    'defaultValue' => [
        'face_img' => 'assets/images/face_img.png', //用户默认头像
        'admin_email' => 'zangsilu@163.com', //系统默认邮箱
    ],
    //快递类型及价格
    'express' => [
        '1' => ['中通快递',10],
        '2' => ['申通快递',15],
        '3' => ['圆通快递',20],
        '4' => ['包邮','0'],
    ],
    //支付方式
    'payType' =>[
        '1' => '支付宝',
        '2' => '微信',
    ]



];
