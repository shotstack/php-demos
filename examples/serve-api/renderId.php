<?php
require __DIR__ . '/../../vendor/autoload.php';

use Shotstack\Client\Api\ServeApi;
use Shotstack\Client\Configuration;

class AssetByRenderIdDemo
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

    public function get($id)
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost($this->apiUrl)
            ->setApiKey('x-api-key', $this->apiKey);

        $client = new ServeApi(null, $config);

        try {
            $response = $client->getAssetByRenderId($id)->getData();
        } catch (Exception $e) {
            die('Request failed or not found: ' . $e->getMessage());
        }

        foreach ($response as $asset) {
            if ($asset->getAttributes()->getStatus() === 'ready') {
                echo "\nStatus: " . strtoupper($asset->getAttributes()->getStatus()) . "\n\n";
                echo ">> Asset CDN URL: " . $asset->getAttributes()->getUrl() . "\n";
                echo ">> Asset ID: " . $asset->getAttributes()->getId() . "\n";
                echo ">> Render ID: " . $asset->getAttributes()->getRenderId() . "\n";
            } else if ($asset->getAttributes()->getStatus() === 'failed') {
                echo ">> Something went wrong, asset could not be copied.\n";
            } else {
                echo ">> Copying in progress, please try again in a few seconds.\n";
            }
        }
    }
}

if (empty($argv[1])) {
    echo ">> Please provide the UUID of the render task (i.e. php examples/serve-api/renderId.php 2abd5c11-0f3d-4c6d-ba20-235fc9b8e8b7)\n";
    return;
}

$demo = new AssetByRenderIdDemo();
$demo->get($argv[1]);
