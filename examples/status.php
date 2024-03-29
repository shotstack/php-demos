<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Client\Api\EditApi;
use Shotstack\Client\ApiException;
use Shotstack\Client\Configuration;

class StatusDemo
{
    protected $apiKey;
    protected $apiUrl = 'https://api.shotstack.io/stage';

    public function __construct()
    {
        if (empty(getenv('SHOTSTACK_KEY'))) {
            die("API Key is required. Set using: export SHOTSTACK_KEY=your_key_here\n");
        }

        if (!empty(getenv('SHOTSTACK_HOST'))) {
            $this->apiUrl = getenv('SHOTSTACK_HOST');
        }

        $this->apiKey = getenv('SHOTSTACK_KEY');
    }

    public function render($id)
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost($this->apiUrl)
            ->setApiKey('x-api-key', $this->apiKey);

        $client = new EditApi(null, $config);

        try {
            $response = $client->getRender($id, false, true)->getResponse();
        } catch (ApiException $e) {
            die('Request failed or not found: ' . $e->getMessage() . $e->getResponseBody());
        }

        echo "\nStatus: " . strtoupper($response->getStatus()) . "\n\n";

        if ($response->getStatus() == 'done') {
            echo ">> Asset URL: " . $response->getUrl() . "\n";
        } else if ($response->getStatus() == 'failed') {
            echo ">> Something went wrong, rendering has terminated and will not continue.\n";
        } else {
            echo ">> Rendering in progress, please try again shortly.\n>> Note: Rendering may take up to 1 minute to complete.\n";
        }
    }
}

if (empty($argv[1])) {
    echo ">> Please provide the UUID of the render task (i.e. php examples/status.php 2abd5c11-0f3d-4c6d-ba20-235fc9b8e8b7)\n";
    return;
}

$demo = new StatusDemo();
$demo->render($argv[1]);
