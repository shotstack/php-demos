<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Client\Api\EditApi;
use Shotstack\Client\Configuration;
use Shotstack\Client\Model\Edit;
use Shotstack\Client\Model\Output;
use Shotstack\Client\Model\Timeline;
use Shotstack\Client\Model\Track;
use Shotstack\Client\Model\Clip;
use Shotstack\Client\Model\ImageAsset;
use Shotstack\Client\Model\Size;

class BorderDemo
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

    public function render()
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost($this->apiUrl)
            ->setApiKey('x-api-key', $this->apiKey);

        $client = new EditApi(null, $config);

        // Border - top layer (track1)
        $borderAsset = new ImageAsset();
        $borderAsset
            ->setSrc('https://shotstack-assets.s3.ap-southeast-2.amazonaws.com/borders/80s-acid-pink-square.png');

        $border = new Clip();
        $border
            ->setAsset($borderAsset)
            ->setStart(0)
            ->setLength(1);

        $track1 = new Track();
        $track1
            ->setClips([$border]);

        // Background image - bottom layer (track2)
        $imageAsset = new ImageAsset();
        $imageAsset
            ->setSrc('https://shotstack-assets.s3.ap-southeast-2.amazonaws.com/images/dolphins.jpg');

        $image = new Clip();
        $image
            ->setAsset($imageAsset)
            ->setStart(0)
            ->setLength(1);

        $track2 = new Track();
        $track2
            ->setClips([$image]);

        $timeline = new Timeline();
        $timeline
            ->setBackground("#000000")
            ->setTracks([$track1, $track2]); // Put track1 first to go above track2

        $output = new Output();
        $output
            ->setFormat('jpg')
            ->setQuality('high')
            ->setSize((new Size())
                ->setWidth(1000)
                ->setHeight(1000)
            );

        $edit = new Edit();
        $edit
            ->setTimeline($timeline)
            ->setOutput($output);

        try {
            $response = $client->postRender($edit)->getResponse();
        } catch (Exception $e) {
            die('Request failed: ' . $e->getMessage());
        }

        echo $response->getMessage() . "\n";
        echo ">> Now check the progress of your render by running:\n";
        echo ">> php examples/status.php " . $response->getId() . "\n";
    }
}

$editor = new BorderDemo();
$editor->render();
