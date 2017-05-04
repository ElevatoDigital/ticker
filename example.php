<?php

require_once __DIR__  . '/vendor/autoload.php';

ticker_init(
    [
        'TICKER' => $_SERVER['argv'][1]
    ]
);

ticker('first-event');

sleep(2);

ticker('second-event');