<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Api\RenderApi;
use Shotstack\ApiClient;
use Shotstack\Configuration;
use Shotstack\Model\Edit;
use Shotstack\Model\Output;
use Shotstack\Model\Soundtrack;
use Shotstack\Model\Timeline;
use Shotstack\Model\Track;
use Shotstack\Model\Transition;
use Shotstack\Model\Clip;
use Shotstack\Model\TitleAsset;
use Shotstack\Model\VideoAsset;

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
        $length = 3;
        $trim = 0;
        $end = $length;

        foreach ($this->filters as $index => $filter) {
            // Video clips
            $videoAsset = new VideoAsset();
            $videoAsset
                ->setVideo('https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/footage/skater.hd.mp4')
                ->setTrim($trim);

            $videoClip = new Clip();
            $videoClip
                ->setAsset($videoAsset)
                ->setLength($length)
                ->setStart($start);

            if ($filter !== 'original') {
                $videoTransition = new Transition();
                $videoTransition->setIn('wipeRight');

                $videoClip
                    ->setTransition($videoTransition)
                    ->setLength($length + 1)
                    ->setFilter($filter);
            }

            $videoClips[] = $videoClip;

            // Title clips
            $titleTransition = new Transition();
            $titleTransition
                ->setIn('fade')
                ->setOut('fade');

            $titleAsset = new TitleAsset();
            $titleAsset
                ->setTitle($filter)
                ->setStyle('minimal');

            $titleClip = new Clip();
            $titleClip
                ->setAsset($titleAsset)
                ->setLength($length - ($start === 0 ? 1 : 0))
                ->setStart($start)
                ->setTransition($titleTransition);

            $titleClips[] = $titleClip;

            $trim = $end - 1;
            $end = $trim + $length + 1;
            $start = $trim;
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
