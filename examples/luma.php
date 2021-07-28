<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Client\Api\EditApi;
use Shotstack\Client\Configuration;
use Shotstack\Client\Model\Edit;
use Shotstack\Client\Model\Output;
use Shotstack\Client\Model\Soundtrack;
use Shotstack\Client\Model\Timeline;
use Shotstack\Client\Model\Track;
use Shotstack\Client\Model\Clip;
use Shotstack\Client\Model\LumaAsset;
use Shotstack\Client\Model\VideoAsset;

class LumaDemo
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

        $soundtrack = new Soundtrack();
        $soundtrack
            ->setEffect("fadeOut")
            ->setSrc("https://shotstack-assets.s3-ap-southeast-2.amazonaws.com/music/unminus/palmtrees.mp3");

        // First luma matte - top layer (track1)
        $lumaAsset1 = new LumaAsset();
        $lumaAsset1
            ->setSrc('https://shotstack-assets.s3-ap-southeast-2.amazonaws.com/examples/luma-mattes/paint-left.mp4');

        $lumaClip1 = new Clip();
        $lumaClip1
            ->setAsset($lumaAsset1)
            ->setStart(3.6)
            ->setLength(1.4);

        $videoAsset1 = new VideoAsset();
        $videoAsset1
            ->setSrc('https://shotstack-assets.s3-ap-southeast-2.amazonaws.com/footage/table-mountain.mp4');

        $videoClip1 = new Clip();
        $videoClip1
            ->setAsset($videoAsset1)
            ->setStart(0)
            ->setLength(5);

        $track1 = new Track();
        $track1
            ->setClips([$lumaClip1, $videoClip1]);

        // Video on second track to be revealed with luma matte to reveal third track
        $lumaAsset2 = new LumaAsset();
        $lumaAsset2
            ->setSrc('https://shotstack-assets.s3-ap-southeast-2.amazonaws.com/examples/luma-mattes/paint-right.mp4');

        $lumaClip2 = new Clip();
        $lumaClip2
            ->setAsset($lumaAsset2)
            ->setStart(7.2)
            ->setLength(1.4);

        $videoAsset2 = new VideoAsset();
        $videoAsset2
            ->setSrc('https://shotstack-assets.s3-ap-southeast-2.amazonaws.com/footage/road.mp4');

        $videoClip2 = new Clip();
        $videoClip2
            ->setAsset($videoAsset2)
            ->setStart(3.6)
            ->setLength(5);

        $track2 = new Track();
        $track2
            ->setClips([$lumaClip2, $videoClip2]);

        // Final video on third track
        $videoAsset3 = new VideoAsset();
        $videoAsset3
            ->setSrc('https://shotstack-assets.s3-ap-southeast-2.amazonaws.com/footage/lake.mp4');

        $videoClip3 = new Clip();
        $videoClip3
            ->setAsset($videoAsset3)
            ->setStart(7.2)
            ->setLength(5);

        $track3 = new Track();
        $track3
            ->setClips([$videoClip3]);

        $timeline = new Timeline();
        $timeline
            ->setBackground("#000000")
            ->setSoundtrack($soundtrack)
            ->setTracks([$track1, $track2, $track3]);

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

$editor = new LumaDemo();
$editor->render();
