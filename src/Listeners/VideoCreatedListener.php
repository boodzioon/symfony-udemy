<?php

namespace App\Listeners;

use App\Events\VideoCreatedEvent;

class VideoCreatedListener
{
    public function onVideoCreatedEvent(VideoCreatedEvent $event)
    {
        dump($event);
        dump($event->getVideo());
    }
}