<?php
require __DIR__ . '/../../vendor/autoload.php';

use Shotstack\Client\Api\EditApi;
use Shotstack\Client\ApiException;
use Shotstack\Client\Configuration;
use Shotstack\Client\Model\TemplateRender;
use Shotstack\Client\Model\MergeField;

class RenderTemplate
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

    public function render(string $id)
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost($this->apiUrl)
            ->setApiKey('x-api-key', $this->apiKey);

        $client = new EditApi(null, $config);

        $mergeFieldUrl = new MergeField();
        $mergeFieldUrl
            ->setFind('URL')
            ->setReplace('https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/footage/skater.hd.mp4');

        $mergeFieldTrim = new MergeField();
        $mergeFieldTrim
            ->setFind('TRIM')
            ->setReplace(3);

        $mergeFieldLength = new MergeField();
        $mergeFieldLength
            ->setFind('LENGTH')
            ->setReplace(6);

        $template = new TemplateRender();
        $template
            ->setId($id)
            ->setMerge([
                $mergeFieldUrl,
                $mergeFieldTrim,
                $mergeFieldLength,
            ]);

        try {
            $response = $client->postTemplateRender($template)->getResponse();
        } catch (ApiException $e) {
            die('Request failed: ' . $e->getMessage() . $e->getResponseBody());
        }

        echo $response->getMessage() . "\n";
        echo ">> Now check the progress of your render by running:\n";
        echo ">> php examples/status.php " . $response->getId() . "\n";
    }
}

if (empty($argv[1])) {
    echo ">> Please provide the UUID of the template (i.e. php examples/templates/render.php 2abd5c11-0f3d-4c6d-ba20-235fc9b8e8b7)\n";
    return;
}

$template = new RenderTemplate();
$template->render($argv[1]);
