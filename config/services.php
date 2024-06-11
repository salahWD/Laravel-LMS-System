<?php

return [

  'stripe' => [
    "key" => env("STRIPE_KEY"),
    "secret" => env("STRIPE_SECRET"),
    "webhook_secret" => env("STRIPE_WEBHOOK_SECRET"),
  ],

  'mailgun' => [
    'domain' => env('MAILGUN_DOMAIN'),
    'secret' => env('MAILGUN_SECRET'),
    'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    'scheme' => 'https',
  ],

  'recaptcha' => [
    'site_key' => env('GOOGLE_RECAPATCHA_SITE_KEY', ''),
    'secret_key' => env('GOOGLE_RECAPATCHA_SECRET_KEY', ''),
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
