# Welcome to the {{ \Redis::hget('live:reddit', 'title') }}!

{{ \Redis::hget('live:description', 'raw') }}

### Watching the launch live

To watch the launch live, pick your preferred streaming provider from the table below. Can't pick? [Read about the differences](/r/spacex/wiki/faq/watching#wiki_i.27m_online._where_can_i_watch_the_launch.2C_what_streams_should_i_watch.2C_and_how_can_i_participate_in_the_discussion.3F).

| [SpaceX Stats Live (Webcasts + Live Updates)](https://spacexstats.com/live) |
| --- |
@if (json_decode(\Redis::hget('live:streams', 'spacex'))->isAvailable)
| **[SpaceX Hosted Webcast (YouTube)](https://youtube.com/watch?v={{ json_decode(\Redis::hget('live:streams', 'spacex'))->youtubeVideoId }})** |
@endif
@if (json_decode(\Redis::hget('live:streams', 'spacexClean'))->isAvailable)
| **[SpaceX Technical Webcast (YouTube)](https://youtube.com/watch?v={{ json_decode(\Redis::hget('live:streams', 'spacexClean'))->youtubeVideoId }})** |
@endif
@if (json_decode(\Redis::hget('live:streams', 'nasa'))->isAvailable)
| **[NASA TV (Ustream)](http://www.ustream.tv/nasahdtv)** |
| **[NASA TV (YouTube)](https://youtube.com/watch?v={{ json_decode(\Redis::hget('live:streams', 'nasa'))->youtubeVideoId }})** |
@endif

### Official Live Updates

| Time | Countdown | Update |
| --- |--- | --- |
@for($i = 0; $i <= 100; $i++)
@if (isset($updates[$i]))
| {{ $updates[$i]->createdAt }} UTC | {{ $updates[$i]->timestamp }} | {{ $updates[$i]->update }} |
@endif
@endfor

@foreach(json_decode(\Redis::get('live:sections'), true) as $section)
### {{ $section['title'] }}

{{ $section['content'] }}
@endforeach

### Useful Resources, Data, ♫, & FAQ
@foreach(json_decode(\Redis::get('live:resources'), true) as $resource)
@if ($resource['courtesy'] != null)
* [{{ $resource['title'] }}]({{ $resource['url'] }}), {{ $resource['courtesy'] }}
@else
* [{{ $resource['title'] }}]({{ $resource['url'] }})
@endif
@endforeach

### Participate in the discussion!

* First of all, launch threads are party threads! We understand everyone is excited, so we relax the rules in these venues. The most important thing is that everyone enjoy themselves :D
* All other threads are fair game. We will remove low effort comments elsewhere!
* Real-time chat on our official Internet Relay Chat (IRC) [#spacex at irc.esper.net](https://kiwiirc.com/client/irc.esper.net/?nick=SpaceX_guest%7C?#SpaceX). Please read the IRC rules [here](https://www.irccloud.com/pastebin/U4CMHwUk) before participating.
* Please post small launch updates, discussions, and questions here, rather than as a separate post. Thanks!

### Previous /r/SpaceX Live Events

Check out previous /r/SpaceX Live events in the [Launch History page](http://www.reddit.com/r/spacex/wiki/launches) on our community Wiki.