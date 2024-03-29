<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Client\Api\EditApi;
use Shotstack\Client\ApiException;
use Shotstack\Client\Configuration;
use Shotstack\Client\Model\Edit;
use Shotstack\Client\Model\Output;
use Shotstack\Client\Model\Soundtrack;
use Shotstack\Client\Model\Timeline;
use Shotstack\Client\Model\Track;
use Shotstack\Client\Model\Clip;
use Shotstack\Client\Model\ImageAsset;

class GifDemo
{
    protected $apiKey;
    protected $apiUrl = 'https://api.shotstack.io/stage';
    protected $images = [
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-712850.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-867452.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-752036.jpeg'
    ];

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

        $clips = [];
        $start = 0;
        $length = 1.5;

        foreach ($this->images as $index => $image) {
            $imageAsset = new ImageAsset();
            $imageAsset->setSrc($image);

            $clip = new Clip();
            $clip
                ->setAsset($imageAsset)
                ->setLength($length)
                ->setStart($start)
                ->setEffect('zoomIn');

            $start += $length;

            $clips[] = $clip;
        }

        $track1 = new Track();
        $track1
            ->setClips($clips);

        $timeline = new Timeline();
        $timeline
            ->setBackground("#000000")
            ->setTracks([$track1]);

        $output = new Output();
        $output
            ->setFormat('gif')
            ->setResolution('preview')
            ->setFps(12)
            ->setRepeat(false);

        $edit = new Edit();
        $edit
            ->setTimeline($timeline)
            ->setOutput($output);

        try {
            $response = $client->postRender($edit)->getResponse();
        } catch (ApiException $e) {
            die('Request failed: ' . $e->getMessage() . $e->getResponseBody());
        }

        echo $response->getMessage() . "\n";
        echo ">> Now check the progress of your render by running:\n";
        echo ">> php examples/status.php " . $response->getId() . "\n";
    }
}

$demo = new GifDemo();
$demo->render();
