<?php
require_once 'Shotstack-php/autoload.php';

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

class Editor
{
    public function render()
    {
        $config = new Configuration();
        $config
            ->setHost($_ENV('SHOTSTACK_HOST'))
            ->setApiKey('x-api-key', $_ENV('SHOTSTACK_KEY'));

        $client = new ApiClient($config);

        $soundtrack = new Soundtrack();
        $soundtrack
            ->setEffect("fadeInOut")
            ->setSrc("https://s3-ap-southeast-2.amazonaws.com/shotstack-public/disco.mp3");

        $titleOptions = new TitleClipOptions();
        $titleOptions
            ->setColor("#FFFFFF")
            ->setFont('Helvetica')
            ->setPosition('center')
            ->setSize(64);

        $title = new TitleClip();
        $title
            ->setType('title')
            ->setSrc('Hello')
            ->setIn(0)
            ->setOut(5)
            ->setStart(0)
            ->setOptions($titleOptions);

        $track1 = new Track();
        $track1
            ->setClips([$title]);

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

        $response = $render->postRender($edit);

        var_export($response);
    }
}

$editor = new Editor();
$editor->render();