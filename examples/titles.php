<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Client\Api\DefaultApi;
use Shotstack\Client\Configuration;
use Shotstack\Client\Model\Edit;
use Shotstack\Client\Model\Output;
use Shotstack\Client\Model\Soundtrack;
use Shotstack\Client\Model\Timeline;
use Shotstack\Client\Model\Track;
use Shotstack\Client\Model\Transition;
use Shotstack\Client\Model\Clip;
use Shotstack\Client\Model\TitleAsset;

class TitlesDemo
{
    protected $apiKey;
    protected $apiUrl = 'https://api.shotstack.io/stage';
    protected $styles = [
        'minimal',
        'blockbuster',
        'vogue',
        'sketchy',
        'skinny',
        'chunk',
        'chunkLight',
        'marker',
        'future',
        'subtitle',
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

        $client = new DefaultApi(null, $config);

        $soundtrack = new Soundtrack();
        $soundtrack
            ->setEffect("fadeInFadeOut")
            ->setSrc("https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/music/dreams.mp3");

        $clips = [];
        $start = 0;
        $length = 4;

        foreach ($this->styles as $index => $style) {
            $title = new TitleAsset();
            $title
                ->setText($style)
                ->setStyle($style);

            $transition = new Transition();
            $transition
                ->setIn('fade')
                ->setOut('fade');

            $clip = new Clip();
            $clip
                ->setAsset($title)
                ->setStart($start)
                ->setLength($length)
                ->setTransition($transition)
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
            ->setSoundtrack($soundtrack)
            ->setTracks([$track1]);

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

$editor = new TitlesDemo();
$editor->render();
