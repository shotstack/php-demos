<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Api\RenderApi;
use Shotstack\ApiClient;
use Shotstack\Configuration;
use Shotstack\Model\Edit;
use Shotstack\Model\Output;
use Shotstack\Model\Soundtrack;
use Shotstack\Model\Timeline;
use Shotstack\Model\ImageClip;
use Shotstack\Model\ImageClipOptions;
use Shotstack\Model\Track;

class ImageDemo
{
    protected $apiKey;
    protected $apiUrl = 'https://api.shotstack.io/stage/';
    protected $images = [
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-712850.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-867452.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-752036.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-572487.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-114977.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-347143.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-206290.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-940301.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-266583.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-539432.jpeg'
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
            ->setSrc("https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/music/gangsta.mp3");

        $clips = [];
        $start = 0;
        $length = 1.5;

        $options = new ImageClipOptions();
        $options->setEffect('zoomIn');

        foreach ($this->images as $index => $image) {
            $clip = new ImageClip();
            $clip
                ->setType('image')
                ->setSrc($image)
                ->setIn(0)
                ->setOut($length)
                ->setStart($start)
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

$demo = new ImageDemo();
$demo->render();
