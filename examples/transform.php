<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Client\Api\EditApi;
use Shotstack\Client\Configuration;
use Shotstack\Client\Model\Edit;
use Shotstack\Client\Model\Output;
use Shotstack\Client\Model\Timeline;
use Shotstack\Client\Model\Track;
use Shotstack\Client\Model\Clip;
use Shotstack\Client\Model\FlipTransformation;
use Shotstack\Client\Model\RotateTransformation;
use Shotstack\Client\Model\SkewTransformation;
use Shotstack\Client\Model\Transformation;
use Shotstack\Client\Model\VideoAsset;

class TransformDemo
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

        $videoAsset = new VideoAsset();
        $videoAsset
            ->setSrc('https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/footage/skater.hd.mp4');

        $rotate = new RotateTransformation();
        $rotate
            ->setAngle(45);

        $skew = new SkewTransformation();
        $skew
            ->setX(0.25)
            ->setY(0.1);

        $flip = new FlipTransformation();
        $flip
            ->setHorizontal(true)
            ->setVertical(true);

        $transformation = new Transformation();
        $transformation
          ->setRotate($rotate)
          ->setSkew($skew)
          ->setFlip($flip);

        $videoClip = new Clip();
        $videoClip
            ->setAsset($videoAsset)
            ->setLength(8)
            ->setStart(0)
            ->setScale(0.6)
            ->setTransform($transformation);

        $track = new Track();
        $track->setClips([$videoClip]);

        $timeline = new Timeline();
        $timeline->setTracks([$track]);

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

$editor = new TransformDemo();
$editor->render();
