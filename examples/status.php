<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Api\RenderApi;
use Shotstack\ApiClient;
use Shotstack\Configuration;

class StatusDemo
{
    const outputUrl = "https://s3-ap-southeast-2.amazonaws.com/shotstack-dev-output/";

    public function render($id)
    {
        $config = new Configuration();
        $config
            ->setHost(getenv('SHOTSTACK_HOST'))
            ->setApiKey('x-api-key', getenv('SHOTSTACK_KEY'));

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
