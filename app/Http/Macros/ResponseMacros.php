<?php

use Illuminate\Support\Facades\Response;

Response::macro('invalid', function($message, $statusCode) {
    return 'this is macro';
});
