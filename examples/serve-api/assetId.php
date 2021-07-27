<?php
require __DIR__ . '/../../vendor/autoload.php';

use Shotstack\Client\Api\ServeApi;
use Shotstack\Client\Configuration;

class AssetByIdDemo
{
    protected $apiKey;
    protected $apiUrl = 'https://api.shotstack.io/serve/stage';

    public function __construct()
    {
        if (empty(getenv('SHOTSTACK_KEY'))) {
            die("API Key is required. Set using: export SHOTSTACK_KEY=your_key_here\n");
        }

        if (!empty(getenv('SHOTSTACK_SERVE_HOST'))) {
            $this->apiUrl = getenv('SHOTSTACK_SERVE_HOST');
        }

        $this->apiKey = getenv('SHOTSTACK_KEY');
    }

    public function render($id)
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost($this->apiUrl)
            ->setApiKey('x-api-key', $this->apiKey);

        $client = new ServeApi(null, $config);

        try {
            $response = $client->getAsset($id)->getData();
        } catch (Exception $e) {
            die('Request failed or not found: ' . $e->getMessage());
        }

        if ($response->getAttributes()->getStatus() === 'ready') {
            echo "\nStatus: " . strtoupper($response->getAttributes()->getStatus()) . "\n\n";
            echo ">> Asset CDN URL: " . $response->getAttributes()->getUrl() . "\n";
            echo ">> Asset ID: " . $response->getAttributes()->getId() . "\n";
            echo ">> Render ID: " . $response->getAttributes()->getRenderId() . "\n";
        } else if ($response->getAttributes()->getStatus() === 'failed') {
            echo ">> Something went wrong, asset could not be copied.\n";
        } else {
            echo ">> Copying in progress, please try again in a few seconds.\n";
        }
    }
}

if (empty($argv[1])) {
    echo ">> Please provide the UUID of the render task (i.e. php examples/asset.php 2abd5c11-0f3d-4c6d-ba20-235fc9b8e8b7)\n";
    return;
}

$demo = new AssetByIdDemo();
$demo->render($argv[1]);
