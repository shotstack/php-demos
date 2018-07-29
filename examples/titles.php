<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Api\RenderApi;
use Shotstack\ApiClient;
use Shotstack\Configuration;
use Shotstack\Model\Edit;
use Shotstack\Model\Output;
use Shotstack\Model\Soundtrack;
use Shotstack\Model\Timeline;
use Shotstack\Model\TitleClip;
use Shotstack\Model\TitleClipOptions;
use Shotstack\Model\Track;
use Shotstack\Model\Transition;

class TitlesDemo
{
    protected $apiKey;
    protected $apiUrl = 'https://api.shotstack.io/dev/';
    protected $styles = [
        'minimal',
        'blockbuster',
        'vogue',
        'sketchy',
        'skinny',
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
        $config = new Configuration();
        $config
            ->setHost($this->apiUrl)
            ->setApiKey('x-api-key', $this->apiKey);

        $client = new ApiClient($config);

        $soundtrack = new Soundtrack();
        $soundtrack
            ->setEffect("fadeInOut")
            ->setSrc("https://s3-ap-southeast-2.amazonaws.com/shotstack-public/dreams.mp3");

        $clips = [];
        $start = 0;
        $length = 4;

        foreach ($this->styles as $index => $style) {
            $options = new TitleClipOptions();
            $options
                ->setEffect($style);

            $transition = new Transition();
            $transition
                ->setIn('fadeIn')
                ->setOut('fadeOut');

            $clip = new TitleClip();
            $clip
                ->setType('title')
                ->setSrc($style)
                ->setIn(0)
                ->setOut($length)
                ->setStart($start)
                ->setTransition($transition)
                ->setOptions($options);

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

        $render = new RenderApi($client);

        try {
            $response = $render->postRender($edit)->getResponse();
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
