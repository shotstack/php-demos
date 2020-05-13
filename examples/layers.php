<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Client\Api\EndpointsApi;
use Shotstack\Client\Configuration;
use Shotstack\Client\Model\Edit;
use Shotstack\Client\Model\Output;
use Shotstack\Client\Model\Soundtrack;
use Shotstack\Client\Model\Timeline;
use Shotstack\Client\Model\Track;
use Shotstack\Client\Model\Clip;
use Shotstack\Client\Model\TitleAsset;
use Shotstack\Client\Model\VideoAsset;

class LayersDemo
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

        $client = new EndpointsApi(null, $config);

        $soundtrack = new Soundtrack();
        $soundtrack
            ->setEffect("fadeOut")
            ->setSrc("https://shotstack-assets.s3-ap-southeast-2.amazonaws.com/music/freepd/fireworks.mp3");

        // Title - top layer (track1)
        $titleAsset = new TitleAsset();
        $titleAsset
            ->setStyle('chunk')
            ->setText('HELLO WORLD')
            ->setSize('x-large');

        $title = new Clip();
        $title
            ->setAsset($titleAsset)
            ->setStart(0)
            ->setLength(10)
            ->setEffect('zoomIn');

        $track1 = new Track();
        $track1
            ->setClips([$title]);

        // Video - bottom layer (track2)
        $videoAsset = new VideoAsset();
        $videoAsset
            ->setSrc('https://shotstack-assets.s3-ap-southeast-2.amazonaws.com/footage/table-mountain.mp4');

        $video = new Clip();
        $video
            ->setAsset($videoAsset)
            ->setStart(0)
            ->setLength(10);

        $track2 = new Track();
        $track2
            ->setClips([$video]);

        $timeline = new Timeline();
        $timeline
            ->setBackground("#000000")
            ->setSoundtrack($soundtrack)
            ->setTracks([$track1, $track2]); // Put track1 first to go above track2

        $output = new Output();
        $output
            ->setFormat('mp4')
            ->setResolution('sd');

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

$editor = new LayersDemo();
$editor->render();