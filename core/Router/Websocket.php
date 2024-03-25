<?php

namespace core\Router;

use ElephantIO\Client;

class Websocket
{
    public Client $client;

    public function __construct($url = 'localhost:8081', $options = [])
    {
        $this->client = Client::create($url, $options);
    }

    public function emit($channel = 'default', $data = []): void
    {
        $this->client->emit($channel, $data);
    }

    public function __destruct()
    {
        $this->client->disconnect();
    }
}