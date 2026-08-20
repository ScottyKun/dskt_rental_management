<?php

return [

    'subject' => env(
        'VAPID_SUBJECT',
        'mailto:dskt.rental@famproject.cloud'
    ),

    'public_key' => env('VAPID_PUBLIC_KEY'),

    'private_key' => env('VAPID_PRIVATE_KEY'),

];