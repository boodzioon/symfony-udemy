<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Events\VideoCreatedEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VideoCreatedSubscriber implements EventSubscriberInterface
{
    public function onVideoCreatedEvent(VideoCreatedEvent $event)
    {
        dump($event->getVideo());
    }

    public function onKernelResponsePre(ResponseEvent $event)
    {
        dump('Pre Response');
    }

    public function onKernelResponsePost(ResponseEvent $event)
    {
        dump('Post Response');
    }

    public static function getSubscribedEvents()
    {
        return [
            'video.created.event' => 'onVideoCreatedEvent',
            KernelEvents::RESPONSE => [
                ['onKernelResponsePre', 2],
                ['onKernelResponsePost', 1]
            ]
        ];
    }
}
