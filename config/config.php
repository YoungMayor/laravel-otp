<?php

return [
    /**
     * Define your OTP Actions here, they should follow the demo format provided 
     * 
     * Array Key (demo.action): This is the action. It can contain alphabets, numerals, fullstop, underscore and hyphen
     * subject: The mail subject
     * greeting: The mail greeting, 
     * message: The mail message, 
     * decay: The PIN expiry time in minutes, 
     * length: Number between 4 - 9. The length of the OTP code
     */
    'actions' => [
        'demo.action.1' => [
            'subject' => 'OTP Action One',
            'greeting' => 'Greetings Chief',
            'message' => 'This is a demo action',
            'decay' => 30,
            'length' => 6
        ],
        'demo.action.2' => [
            'subject' => 'Demo OTP Two',
            'greeting' => 'Greetings Chief',
            'message' => 'This is another demo action',
            'decay' => 5,
            'length' => 4
        ]
    ]
];
