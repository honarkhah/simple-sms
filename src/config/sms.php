<?php

/**
    |--------------------------------------------------------------------------
    | Simple SMS
    |--------------------------------------------------------------------------
    | Driver
    |   kavenegar: http://kavenegar.com/
    |   mellipayamak: http://www.melipayamak.com/
    |   Twilio: https://www.twilio.com/
    |--------------------------------------------------------------------------
    | Twilio Additional Settings
    |   Account SID:  The Account SID associated with your Twilio account. (https://www.twilio.com/user/account/settings)
    |   Auth Token:   The Auth Token associated with your Twilio account. (https://www.twilio.com/user/account/settings)
    |   Verify:       Ensures extra security by checking if requests
    |                 are really coming from Twilio.
    |--------------------------------------------------------------------------
 */

return [
    'driver' => 'kevenegar',
    'from' => 'Your Number or Email',
    'twilio' => [
        'account_sid' => 'Your SID',
        'auth_token' => 'Your Token',
        'verify' => true,
    ],
    'melipayamak' => [
        'username' => 'Your Melipayamak Username',
        'password' => 'Your Melipayamak Password',
        'lineNumbers' => [
            'NONE'
        ],
    ],
    'kavenegar' => [
        'api_key' => 'Sample Api Key',
        'api_path' => 'http://api.kavenegar.com/v1/%s/%s/%s.json/',
        'line_numbers' => [
            'default' => '+981000',
            'list' => [
            ]
        ]
    ]
];
