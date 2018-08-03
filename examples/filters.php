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
use Shotstack\Model\VideoClip;
use Shotstack\Model\VideoClipOptions;

class FiltersDemo
{
    protected $apiKey;
    protected $apiUrl = 'https://api.shotstack.io/stage/';
    protected $filters = [
        'original',
        'boost',
        'contrast',
        'muted',
        'darken',
        'lighten',
        'greyscale',
        'negative',
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
            ->setSrc("https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/music/freeflow.mp3");

        $videoClips = [];
        $titleClips = [];
        $start = 0;
        $length = 4;
        $in = 0;
        $out = $length;

        foreach ($this->filters as $index => $filter) {
            $transition = new Transition();
            $transition
                ->setIn('fadeIn')
                ->setOut('fadeOut');

            // Video effect clips
            $videoOptions = new VideoClipOptions();
            if ($filter !== 'original') {
                $videoOptions->setFilter($filter);
            }

            $videoClip = new VideoClip();
            $videoClip
                ->setType('video')
                ->setSrc('https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/footage/cat.mp4')
                ->setIn($in)
                ->setOut($out)
                ->setStart($start)
                ->setTransition($transition)
                ->setOptions($videoOptions);

            $videoClips[] = $videoClip;

            // Title clips
            $titleOptions = new TitleClipOptions();
            $titleOptions
                ->setEffect('minimal');

            $titleClip = new TitleClip();
            $titleClip
                ->setType('title')
                ->setSrc($filter)
                ->setIn(0)
                ->setOut($length)
                ->setStart($start)
                ->setTransition($transition)
                ->setOptions($titleOptions);

            $titleClips[] = $titleClip;

            $in += 2;
            $out = $in + $length;
            $start += $length;
        }

        $track1 = new Track();
        $track1
            ->setClips($titleClips);

        $track2 = new Track();
        $track2
            ->setClips($videoClips);

        $timeline = new Timeline();
        $timeline
            ->setBackground("#000000")
            ->setSoundtrack($soundtrack)
            ->setTracks([$track1, $track2]);

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

$editor = new FiltersDemo();
$editor->render();
