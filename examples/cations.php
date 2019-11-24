<?php
require __DIR__ . '/../vendor/autoload.php';

// Import the SDK components and SRT Parser
use Shotstack\Client\Api\EndpointsApi;
use Shotstack\Client\Configuration;
use Shotstack\Client\Model\Edit;
use Shotstack\Client\Model\Output;
use Shotstack\Client\Model\VideoAsset;
use Shotstack\Client\Model\Timeline;
use Shotstack\Client\Model\Track;
use Shotstack\Client\Model\Clip;
use Shotstack\Client\Model\TitleAsset;
use Shotstack\Client\Model\Offset;
use Shotstack\Client\Model\Soundtrack;
use Benlipp\SrtParser\Parser;

/**
 * A demonstration to shows how to parse an SRT file 
 * and add captions to a video.
 */
class CationsDemo
{
    protected $apiKey;
    protected $apiUrl = 'https://api.shotstack.io/stage';

    /**
     * Set the API key and host
     */
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

    /**
     * Main render method
     */
    public function render()
    {
        // Configure the SDK client
        $config = Configuration::getDefaultConfiguration()
            ->setHost($this->apiUrl)
            ->setApiKey('x-api-key', $this->apiKey);

        $client = new EndpointsApi(null, $config);

        // Prepare the sountrack
        $soundtrack = new Soundtrack();
        $soundtrack
            ->setSrc("https://shotstack-pubic-files.s3-ap-southeast-2.amazonaws.com/examples/cat-meowing.mp3");

        // Prepare the background video clip
        $videoAsset = new VideoAsset();
        $videoAsset
            ->setSrc('https://shotstack-pubic-files.s3-ap-southeast-2.amazonaws.com/examples/cat-meowing.mp4');

        $videoClip = new Clip();
        $videoClip
            ->setAsset($videoAsset)
            ->setLength(17.40)
            ->setStart(0);

        // Parse the SRT file to an array of captions
        $parser = new Parser();
        $parser->loadFile(__DIR__ . '/assets/transcript.srt');
        $captions = $parser->parse();

        $clips = [];

        // Loop through the captions and create text clips
        foreach ($captions as $caption) {
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

        // Add the text clips to the top track
        $track1 = new Track();
        $track1
            ->setClips($clips);

        // Add the video clip to the bottom track underneath the text
        $track2 = new Track();
        $track2
            ->setClips([$videoClip]);

        // Add everything to a timeline
        $timeline = new Timeline();
        $timeline
            ->setBackground("#000000")
            ->setSoundtrack($soundtrack)
            ->setTracks([$track1,$track2]);

        // Specify the output format
        $output = new Output();
        $output
            ->setFormat('mp4')
            ->setResolution('sd');

        // Finalise the edit
        $edit = new Edit();
        $edit
            ->setTimeline($timeline)
            ->setOutput($output);

        // Post to the API
        try {
            $response = $client->postRender($edit)->getResponse();
        } catch (Exception $e) {
            print_r($e->getMessage());
            die('Request failed: ' . $e->getMessage());
        }

        // Await response
        echo $response->getMessage() . "\n";
        echo ">> Now check the progress of your render by running:\n";
        echo ">> php examples/status.php " . $response->getId() . "\n";
    }
}

$demo = new CationsDemo();
$demo->render();
