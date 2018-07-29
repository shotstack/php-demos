<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Api\RenderApi;
use Shotstack\ApiClient;
use Shotstack\Configuration;

class StatusDemo
{
    protected $apiKey;
    protected $apiUrl = 'https://api.shotstack.io/dev/';
    const outputUrl = "https://s3-ap-southeast-2.amazonaws.com/shotstack-api-dev-output/";

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
        $config = new Configuration();
        $config
            ->setHost($this->apiUrl)
            ->setApiKey('x-api-key', $this->apiKey);

        $client = new ApiClient($config);
        $render = new RenderApi($client);

        try {
            $response = $render->getRender($id)->getResponse();
        } catch (Exception $e) {
            die('Request failed or not found: ' . $e->getMessage());
        }

        echo "Status: " . strtoupper($response->getStatus()) . "\n";

        if ($response->getStatus() == 'done') {
            echo ">> Video URL: " . self::outputUrl . $response->getOwner() . DIRECTORY_SEPARATOR . $response->getId() . ".mp4\n";
        } else if ($response->getStatus() == 'failed') {
            echo ">> Something went wrong, rendering has terminated and will not continue.\n";
        } else {
            echo ">> Rendering in progress, please try again shortly.\n";
        }
    }
}

if (empty($argv[1])) {
    echo ">> Please provide the UUID of the render task (i.e. php examples/status.php 2abd5c11-0f3d-4c6d-ba20-235fc9b8e8b7)\n";
    return;
}

$demo = new StatusDemo();
$demo->render($argv[1]);
