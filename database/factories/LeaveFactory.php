<?php

/* @var $factory Factory */

use App\Models\Leave;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Date;

$factory->define(Leave::class, function () {
    return [
        'status' => Leave::STATUS_WAIT_FOR_APPROVE,
        'start' => Date::now(),
        'end' => Date::createFromTimestamp(time() + 86400),
    ];
});
