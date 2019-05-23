<?php
return [
    'adminEmail' => 'admin@example.com',
    'lottery_limit_per_day' => 5,
    'api_hash' => [
        'member' => 
        [
            'lottery' => 
            [
                [['code'], 'required'],
                [['code'], 'string'],
            ],
            'lottery-history' => 
            [     
                [['from_date', 'to_date'], 'required'],
                [['from_date', 'to_date'], 'dateTime'],
                [['code', 'mobile'], 'set'],
                [['code'], 'string'],
            ],
            'redeem-prize' => 
            [     
                [['code', 'record_id'], 'required'], 
                [['code'], 'string'],
            ]
        ],
        'token' =>
        [
            'get' =>
            [
                [['username', 'password', 'account_id'],'required'],
            ],
            'refresh' => 
            [
                [['refresh_token'],'required'],
            ]
        ]            
    ]
];
