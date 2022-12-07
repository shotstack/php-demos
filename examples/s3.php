<?php
/**
 * S3 Destinations Demo
 * Ensure S3 credentials are configured in the Shotstack dashboard before running this demo.
 * See: https://shotstack.io/docs/guide/serving-assets/destinations/s3
 */
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
use Shotstack\Client\Model\S3Destination;
use Shotstack\Client\Model\S3DestinationOptions;
use Shotstack\Client\Model\ShotstackDestination;

class S3Demo
{
  // Replace with your own configuration
    const S3_REGION = 'us-east-1';
    const S3_BUCKET = 'my-bucket';
    const S3_ACL = 'public-read';

    protected $apiKey;
    protected $apiUrl = 'https://api.shotstack.io/stage';
    protected $images = [
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-712850.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-867452.jpeg',
        'https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/examples/images/pexels/pexels-photo-752036.jpeg',
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

        $soundtrack = new Soundtrack();
        $soundtrack
            ->setEffect("fadeInFadeOut")
            ->setSrc("https://s3-ap-southeast-2.amazonaws.com/shotstack-assets/music/gangsta.mp3");

        $clips = [];
        $start = 0;
        $length = 4;

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

        $track = new Track();
        $track
            ->setClips($clips);

        $timeline = new Timeline();
        $timeline
            ->setBackground("#000000")
            ->setSoundtrack($soundtrack)
            ->setTracks([$track]);

        // Exclude from Shotstack hosting
        $shotstackDestination = new ShotstackDestination();
        $shotstackDestination
            ->setExclude(true);

        // Send to S3 bucket
        $s3Destination = new S3Destination();
        $s3DestinationOptions = new S3DestinationOptions();

        $s3DestinationOptions
            ->setRegion(self::S3_REGION)
            ->setBucket(self::S3_BUCKET)
            ->setAcl(self::S3_ACL);
        $s3Destination
            ->setOptions($s3DestinationOptions);
  
        $output = new Output();
        $output
            ->setFormat('mp4')
            ->setResolution('sd')
            ->setDestinations([
                $shotstackDestination,
                $s3Destination,
            ]);

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

$demo = new S3Demo();
$demo->render();
