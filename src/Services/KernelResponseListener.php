<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class KernelResponseListener {

    public function onKernelResponse(ResponseEvent $event)
    {
        $response = new Response('Response');
        // $event->setResponse($response);
    }
}
