<?php
require __DIR__ . '/../vendor/autoload.php';

use Shotstack\Client\Api\EditApi;
use Shotstack\Client\Configuration;

class ProbeDemo
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

    public function render($url)
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost($this->apiUrl)
            ->setApiKey('x-api-key', $this->apiKey);

        $client = new EditApi(null, $config);

        try {
            $response = $client->probe($url)->getResponse();
        } catch (Exception $e) {
            die('Request failed or not found: ' . $e->getMessage());
        }

        foreach ($response['metadata']->streams as $stream) {
            if ($stream->codec_type === 'video') {
                echo 'Example settings for: ' . $response['metadata']->format->filename, PHP_EOL, PHP_EOL;
                echo 'Width: ' . $stream->width . 'px', PHP_EOL;
                echo 'Height: ' . $stream->height . 'px', PHP_EOL;
                echo 'Framerate: ' . $stream->r_frame_rate . ' fps', PHP_EOL;
                echo 'Duration: ' . $stream->duration . ' secs', PHP_EOL;
            }
        }
    }
}

if (empty($argv[1])) {
    echo ">> Please provide the URL to a media file to inspect (i.e. php examples/probe.php https://github.com/shotstack/test-media/raw/main/captioning/scott-ko.mp4)\n";
    return;
}

$demo = new ProbeDemo();
$demo->render($argv[1]);
