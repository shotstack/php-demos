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
    protected $images = [
        'https://instagram.fsyd4-1.fna.fbcdn.net/vp/dbb49eb3a6b1d8f93574a4466281376c/5B9907F9/t51.2885-15/e35/30855869_607493892937925_2919213808515809280_n.jpg',
        'https://instagram.fsyd4-1.fna.fbcdn.net/vp/b8f98bdb0574c09d3cc46043300d5799/5B9CAA8B/t51.2885-15/e35/30602973_217663802159980_9022839683092054016_n.jpg',
        'https://instagram.fsyd4-1.fna.fbcdn.net/vp/23bfde63c5a3038f19b3ad458a2d674a/5B8AD4A2/t51.2885-15/e35/29739970_451932875244682_6280402497936818176_n.jpg',
        'https://instagram.fsyd4-1.fna.fbcdn.net/vp/611c2f06ddc05c054d6eabcabf5e744d/5B7D38A1/t51.2885-15/e35/29416270_2025882801010968_2671047398402293760_n.jpg',
        'https://instagram.fsyd4-1.fna.fbcdn.net/vp/cfb4f3354d6771fec533f2a942641465/5B834C94/t51.2885-15/e35/30590848_1554502908010575_734443195777155072_n.jpg',
        'https://instagram.fsyd4-1.fna.fbcdn.net/vp/07e76fdd171e50bd4ce5031ce2e7fafa/5B862C45/t51.2885-15/e35/20766953_162715847638292_6272806345762668544_n.jpg',
        'https://instagram.fsyd4-1.fna.fbcdn.net/vp/d17ccb8769c58c881e3ea560f9c6ebf9/5B92DFA1/t51.2885-15/s1080x1080/e35/31556844_205478080059011_9193155683501998080_n.jpg',
        'https://instagram.fsyd4-1.fna.fbcdn.net/vp/6bf65c576c4c9cec37bc419c362cb200/5B9354EA/t51.2885-15/e35/29416299_375115939634496_6286922859587567616_n.jpg',
        'https://instagram.fsyd4-1.fna.fbcdn.net/vp/0a93fa37425839ca406bc8d431ca13de/5B9280A2/t51.2885-15/e35/23507467_1499925020057007_3035019222171254784_n.jpg',
        'https://instagram.fsyd4-1.fna.fbcdn.net/vp/91b8f09923db59b965e743b16596e32e/5B83305C/t51.2885-15/e35/22857792_176133549630686_306757735490256896_n.jpg',
        'https://instagram.fsyd4-1.fna.fbcdn.net/vp/45d52aa7b183754f853aaddd429a8632/5B974E58/t51.2885-15/e35/22278301_293291384508076_8722100403669303296_n.jpg',
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
