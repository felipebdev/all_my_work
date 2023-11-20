<?php

return [
    'jwt_checkout' => env('JWT_CHECKOUT'), // Used on Checkout
    'jwt_platform' => env('JWT_PLATFORM'), // Used for "Change Card" platform
    'jwt_web' => env('JWT_WEB'), // Used on Web Platform (Xgrow platform)
    'jwt_magic' => env('JWT_MAGIC_SECRET'), // Used by magicToken
    'jwt_students' => env('JWT_STUDENTS_SECRET'), // Used on route group students
];
