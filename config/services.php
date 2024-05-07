<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    'facebook' => [
        'client_id' => '321027433639894',
        'client_secret' => 'b77988af8375e92616326c6252bba4fd',
        'redirect' => env('http://localhost:8000/auth/facebook/callback'),
    ],

    'google' => [
        'client_id' => '54009809748-i8tcfv4udn9b1k6ggsm2vm3pqpv2n5jl.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => 'http://localhost:8000/authorized/google/callback',
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
