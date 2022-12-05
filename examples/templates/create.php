<?php
require __DIR__ . '/../../vendor/autoload.php';

use Shotstack\Client\Api\EditApi;
use Shotstack\Client\ApiException;
use Shotstack\Client\Configuration;
use Shotstack\Client\Model\Edit;
use Shotstack\Client\Model\Output;
use Shotstack\Client\Model\Timeline;
use Shotstack\Client\Model\Track;
use Shotstack\Client\Model\Clip;
use Shotstack\Client\Model\VideoAsset;
use Shotstack\Client\Model\Template;

class CreateTemplate
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

    public function post()
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost($this->apiUrl)
            ->setApiKey('x-api-key', $this->apiKey);

        $client = new EditApi(null, $config);

        $videoAsset = new videoAsset();
        $videoAsset
            ->setSrc('{{ URL }}')
            ->setTrim('{{ TRIM }}');

        $videoClip = new Clip();
        $videoClip
            ->setAsset($videoAsset)
            ->setStart(0)
            ->setLength('{{ LENGTH }}');

        $track = new Track();
        $track
            ->setClips([$videoClip]);

        $timeline = new Timeline();
        $timeline
            ->setTracks([$track]);

        $output = new Output();
        $output
            ->setFormat('mp4')
            ->setResolution('sd');

        $edit = new Edit();
        $edit
            ->setTimeline($timeline)
            ->setOutput($output);

        $template = new Template();
        $template
            ->setName('Trim Template')
            ->setTemplate($edit);

        try {
            $response = $client->postTemplate($template)->getResponse();
        } catch (ApiException $e) {
            die('Request failed: ' . $e->getMessage() . $e->getResponseBody());
        }

        echo $response->getMessage() . "\n";
        echo ">> Now get the template details using:\n";
        echo ">> php examples/templates/get.php " . $response->getId() . "\n\n";
        echo ">> or render the template using:\n";
        echo ">> php examples/templates/render.php " . $response->getId() . "\n\n";
    }
}

$template = new CreateTemplate();
$template->post();