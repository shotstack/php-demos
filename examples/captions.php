<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Client\Api\DefaultApi;
use Shotstack\Client\Configuration;
use Shotstack\Client\Model\Edit;
use Shotstack\Client\Model\Output;
use Shotstack\Client\Model\VideoAsset;
use Shotstack\Client\Model\Timeline;
use Shotstack\Client\Model\Track;
use Shotstack\Client\Model\Clip;
use Shotstack\Client\Model\TitleAsset;
use Shotstack\Client\Model\Offset;
use Benlipp\SrtParser\Parser;

class CaptionsDemo
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

        $client = new DefaultApi(null, $config);

        $videoAsset = new VideoAsset();
        $videoAsset
            ->setSrc('https://shotstack-assets.s3-ap-southeast-2.amazonaws.com/examples/captions-example.mp4')
            ->setVolume(1);

        $videoClip = new Clip();
        $videoClip
            ->setAsset($videoAsset)
            ->setLength(21.08)
            ->setStart(0);

        $clips = [];

        $parser = new Parser();
        $parser->loadFile(__DIR__ . '/assets/transcript.srt');
        $captions = $parser->parse();

        foreach ($captions as $index => $caption) {
            $captionAsset = new TitleAsset();
            $captionAsset
                ->setText(wordwrap($caption->text, 80, "\n"))
                ->setStyle('subtitle')
                ->setBackground('#000000')
                ->setPosition('bottom')
                ->setOffset((new Offset)->setY(-0.4))
                ->setSize('small');

            $clip = new Clip();
            $clip
                ->setAsset($captionAsset)
                ->setLength($caption->endTime - ($caption->startTime + 0.1) )
                ->setStart($caption->startTime);

            $clips[] = $clip;
        }

        $track1 = new Track();
        $track1
            ->setClips($clips);

        $track2 = new Track();
        $track2
            ->setClips([$videoClip]);

        $timeline = new Timeline();
        $timeline
            ->setTracks([$track1,$track2]);

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
            print_r($e->getMessage());
            die('Request failed: ' . $e->getMessage());
        }

        echo $response->getMessage() . "\n";
        echo ">> Now check the progress of your render by running:\n";
        echo ">> php examples/status.php " . $response->getId() . "\n";
    }
}

$demo = new CaptionsDemo();
$demo->render();
