<?php

require __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Cloudinary;

/*
|--------------------------------------------------------------------------
| CLOUDINARY CONFIG (PRODUCTION SAFE)
|--------------------------------------------------------------------------
| Move these to environment variables later for better security
|--------------------------------------------------------------------------
*/

$cloud_name = getenv('CLOUDINARY_CLOUD_NAME') ?: 'djngtqjs8';
$api_key    = getenv('CLOUDINARY_API_KEY') ?: '192215628574648';
$api_secret = getenv('CLOUDINARY_API_SECRET') ?: '28DaLWm5wjH3EQ5cvYYZscSH';

/*
|--------------------------------------------------------------------------
| INIT CLOUDINARY
|--------------------------------------------------------------------------
*/

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => $cloud_name,
        'api_key'    => $api_key,
        'api_secret' => $api_secret
    ]
]);
