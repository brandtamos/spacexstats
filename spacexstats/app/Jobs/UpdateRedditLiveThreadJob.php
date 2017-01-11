<?php

namespace SpaceXStats\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use LukeNZ\Reddit\Reddit;
use Illuminate\Contracts\Bus\SelfHandling;
use LukeNZ\Reddit\TokenStorageMethod;

class UpdateRedditLiveThreadJob extends Job implements SelfHandling, ShouldQueue
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Rerender content
        $templatedOutput = view('templates.livethreadcontents')->with([
            'updates' => collect(Redis::lrange('live:updates', 0, -1))->reverse()->map(function($update) {
                return json_decode($update);
            })
        ])->render();

        // Connect to Reddit
        $reddit = new Reddit(Config::get('services.reddit.username'), Config::get('services.reddit.password'), Config::get('services.reddit.id'), Config::get('services.reddit.secret'));
        $reddit->setUserAgent('webapp:elongatedmuskrat:0.1 (by /u/EchoLogic) uses LukeNZ/phpreddit');
        $reddit->setTokenStorageMethod(TokenStorageMethod::Redis);

        // Update Thread
        $reddit->thing(Redis::hget('live:reddit', 'thing'))->edit($templatedOutput);
    }
}
