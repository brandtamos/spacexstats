<?php

namespace SpaceXStats\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use SpaceXStats\Events\WebcastStartedEvent;
use SpaceXStats\Events\WebcastEndedEvent;
use SpaceXStats\Models\WebcastStatus;

class WebcastCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webcast:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the SpaceX Youtube Channel to see if it is running and fetch the viewer count.';

    protected $channelName = 'spacexchannel';
    protected $channelID = 'UCtI0Hodo5o5dUb67FeUjDeA'; // https://developers.google.com/youtube/v3/docs/channels/list#try-it

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $youtube = new Client();

        // Fetch the search responses from youtube for SpaceX's channel
        $upcomingSearchResponse = json_decode($youtube->get('https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=' .
            $this->channelID .
            '&eventType=upcoming&type=video&key=' .
            Config::get('services.youtube.key'))->getBody());

        $liveSearchResponse = json_decode($youtube->get('https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=' .
            $this->channelID .
            '&eventType=live&type=video&key=' .
            Config::get('services.youtube.key'))->getBody());

        // Create boolean variables
        $this->isUpcoming = $upcomingSearchResponse->pageInfo->totalResults != 0;
        $this->isLive = $liveSearchResponse->pageInfo->totalResults != 0;

        // Determine the total number of viewers
        if ($this->isLive || $this->isUpcoming) {

            $searchResponse = $this->isLive ? $liveSearchResponse : $upcomingSearchResponse;

            // Foreach search resource, pull the results
            foreach ($searchResponse->items as $searchItem) {

                // Determine which live stream is which
                if (strpos($searchItem->snippet->title, 'Technical') !== false) {
                    $videos['spacexClean'] = $searchItem->id->videoId;
                } else {
                    $videos['spacex'] = $searchItem->id->videoId;
                }
            }

            // Create a string to query and return the video resources
            $videoIdsString = implode(',', $videos);

            $videoResponse = json_decode($youtube->get('https://www.googleapis.com/youtube/v3/videos?part=liveStreamingDetails&id=' .
                $videoIdsString .
                '&key=' .
                Config::get('services.youtube.key'))->getBody());

            // Count up the viewers
            $this->viewers = 0;
            if ($this->isLive) {
                foreach ($videoResponse->items as $video) {
                    $this->info(json_encode($video));
                    $this->viewers += $video->liveStreamingDetails->concurrentViewers;
                }
            }

        } else {
            $this->viewers = 0;
        }

        // If the livestream is active now, and wasn't before, or vice versa, send an event
        if ($this->isLiveTurningOn()) {
            event(new WebcastStartedEvent($videos));
        } elseif ($this->isLiveTurningOff()) {
            event(new WebcastEndedEvent());
        }

        // Set the related Redis properties
        $this->setLiveStatus();

        // Add to Database if livestream is active
        if ($this->isLive) {
            WebcastStatus::create([
                'viewers' => $this->viewers
            ]);
        }
    }

    private function isLiveTurningOn() {
        return ($this->isLive || $this->isUpcoming) && (Redis::hget('webcast', 'isLive') == 'false' || !Redis::hexists('webcast', 'isLive'));
    }

    private function isLiveTurningOff() {
        return !$this->isLive &&  Redis::hget('webcast', 'isLive') == 'true';
    }

    private function setLiveStatus() {
        Redis::hmset('webcast', 'isLive', ($this->isLive === true || $this->isUpcoming === true) ? 'true' : 'false', 'viewers', $this->viewers);
    }
}
