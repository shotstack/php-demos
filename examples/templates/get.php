<?php
require __DIR__ . '/../../vendor/autoload.php';

use Shotstack\Client\Api\EditApi;
use Shotstack\Client\ApiException;
use Shotstack\Client\Configuration;

class GetTemplate
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

    public function fetch($id)
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost($this->apiUrl)
            ->setApiKey('x-api-key', $this->apiKey);

        $client = new EditApi(null, $config);

        try {
            $response = $client->getTemplate($id);
        } catch (ApiException $e) {
            die('Request failed or not found: ' . $e->getMessage());
        }

        echo "\nTemplate found\n\n";
        echo ">> Template name: " . $response->getResponse()->getName() . "\n";
        echo ">> Template JSON: " . $response->getResponse()->getTemplate() . "\n\n";
        echo ">> Now render the template using:\n";
        echo ">> php examples/templates/render.php " . $response->getResponse()->getId() . "\n\n";
    }
}

if (empty($argv[1])) {
    echo ">> Please provide the UUID of the template (i.e. php examples/templates/get.php 2abd5c11-0f3d-4c6d-ba20-235fc9b8e8b7)\n";
    return;
}

$template = new GetTemplate();
$template->fetch($argv[1]);
