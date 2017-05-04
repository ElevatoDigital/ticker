<?php

use Ticker\Ticker;

function ticker_init($requestContainer = null, $requestName = 'TICKER', $startTime = null)
{
    Ticker::getInstance($requestContainer, $requestName, $startTime);
}

function ticker($id)
{
    Ticker::getInstance()->tick($id);
}
