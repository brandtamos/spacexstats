<?php

namespace SpaceXStats\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use LukeNZ\Reddit\ArrayOptions\Listing;
use LukeNZ\Reddit\Reddit;

class IRCRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'irc:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connect to #spacex on EsperNet and do various things.';

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
        // Configuration
        $server     = 'irc.esper.net';
        $port       = 6667;
        $nickname   = 'ElongatedMuskrat';
        $ident      = 'elongatedmuskrat';
        $gecos      = 'ElongatedMuskrat01';
        $channel    = '#spacexfun';

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $error = socket_connect($socket, $server, $port);

        // Create a Reddit connection
        $reddit = new Reddit(Config::get('services.reddit.username'), Config::get('services.reddit.password'), Config::get('services.reddit.id'), Config::get('services.reddit.secret'));

        if ($socket === false) {
            $errorCode = socket_last_error();
        } else {
            // Registration
            socket_write($socket, "NICK $nickname\r\n");
            socket_write($socket, "USER $ident * 8 :$gecos\r\n");

            while (is_resource($socket)) {
                // Fetch socket data
                $response = trim(socket_read($socket, 1024, PHP_NORMAL_READ));
                $arrayedResponse = explode(' ', $response);
                $arrayedResponse = array_pad($arrayedResponse, 10, '');

                echo $response . "\n";

                // Respond to ping requests
                if ($arrayedResponse[0] === 'PING') {
                    socket_write($socket, 'PONG ' . $arrayedResponse[1] . "\r\n");
                }

                // Channel join
                if ($arrayedResponse[1] === '376' || $arrayedResponse[1] === '422') {
                    socket_write($socket, "JOIN $channel \r\n");
                }

                /*// Only process a Reddit request every 30 seconds
                if (!isset($time)) {
                    $time = time();
                    echo $time;
                }
                if ($time < (time() - 5)) {
                    //$listing = new Listing();
                    //$response = $reddit->subreddit('spacex')->newListings($listing);

                    //socket_write($socket, "PRIVMSG $channel :this worked!\r\n");
                    $time = time();
                    echo $time;
                }*/

                echo time();

                usleep(1000 * 10);
            }
        }
    }
}
