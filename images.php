<?php
require_once 'Shotstack-php/autoload.php';

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
    protected $images = [
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/20969174_416755835388675_5389919747271819264_n.jpg',
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/20969334_434338823626500_5445779374664056832_n.jpg',
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/20905534_752124628293781_3968311114466328576_n.jpg',
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/20986757_1471316216282225_7395950537163669504_n.jpg',
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/20478429_1399520810138594_2192393681472847872_n.jpg',
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/20214127_511632789176343_4146933134950137856_n.jpg',
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/20181002_1520102734717691_6701003053186678784_n.jpg',
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/20067302_321587328294605_5914700897564229632_n.jpg',
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/20225812_417700898630318_295606969597689856_n.jpg',
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/20181217_261348664361174_4900996746357768192_n.jpg',
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/19622823_112920869332479_1004303515767537664_n.jpg',
        'https://scontent-syd2-1.cdninstagram.com/t51.2885-15/s1080x1080/e35/19625026_639481769590952_4765264861997301760_n.jpg'
    ];

    public function render()
    {
        $config = new Configuration();
        $config
            ->setHost(getenv('SHOTSTACK_HOST'))
            ->setApiKey('x-api-key', getenv('SHOTSTACK_KEY'));

        $client = new ApiClient($config);

        $soundtrack = new Soundtrack();
        $soundtrack
            ->setEffect("fadeInOut")
            ->setSrc("https://s3-ap-southeast-2.amazonaws.com/shotstack-public/gangsta.mp3");

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

        $response = $render->postRender($edit);

        var_export($response);
    }
}

$demo = new ImageDemo();
$demo->render();
