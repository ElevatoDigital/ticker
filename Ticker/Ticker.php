<?php

namespace Ticker;

class Ticker
{
    private static $instance;

    private $enabled = false;

    private $startTime;

    private $lastEventTime;

    private $port;

    private $key;

    public static function getInstance($requestContainer = null, $requestName = 'TICKER', $startTime = null)
    {
        if (!self::$instance) {
            self::$instance = new Ticker();
            self::$instance->init($requestContainer, $requestName, $startTime);
        }

        return self::$instance;
    }

    public function init($requestContainer = null, $requestName = 'TICKER', $startTime = null)
    {
        $container = ($requestContainer ?: $_REQUEST);

        if (isset($container[$requestName]) && $container[$requestName]) {
            $tickerJson = base64_decode($container[$requestName]);
            $tickerData = @json_decode($tickerJson, true);

            if (!isset($tickerData['port']) || !isset($tickerData['key'])) {
                return;
            }

            $this->key           = $tickerData['key'];
            $this->port          = $tickerData['port'];
            $this->enabled       = true;
            $this->startTime     = ($startTime ?: $_SERVER['REQUEST_TIME_FLOAT']);
            $this->lastEventTime = $this->startTime;

            $this->tick('request-started', ($startTime ?: $_SERVER['REQUEST_TIME_FLOAT']));
            $this->tick('ticker-started');

            register_shutdown_function(
                function () {
                    $this->tick('request-ended');
                }
            );
        }
    }

    public function tick($id, $time = null)
    {
        if (!$this->enabled) {
            return $this;
        }

        if (null === $time) {
            $time = microtime(true);
        }

        $this->send(
            [
                'id'            => $id,
                'fromStart'     => $time - $this->startTime,
                'fromLastEvent' => $time - $this->lastEventTime
            ]
        );

        $this->lastEventTime = $time;
    }

    private function send(array $data)
    {
        $data['key'] = $this->key;

        $json   = json_encode($data);
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        @socket_connect($socket, '127.0.0.1', $this->port);
        @socket_write($socket, $json, strlen($json));
        socket_close($socket);
    }
}
