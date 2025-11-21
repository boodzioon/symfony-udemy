<?php

namespace App\Services;

use App\Services\OptionalServiceTrait;
use Doctrine\ORM\Event\PostFlushEventArgs;

class MyService implements ServiceInterface
{

    private $service;

    public $logger;
    public $my;

    public function __construct($param, $adminEmail, $globalParam, $service)
    {
        dump($param);
        // dump($adminEmail);
        // dump($globalParam);

        $this->service = $service;
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        dump('Flush!');
        dump($args);
    }

    public function clear()
    {
        dump('Cache Cleared for MyService!!!');
    }

    public function doSomething()
    {
        dump($this->service);
        $this->service->doSomething();
        dump($this->service);

        dump($this->logger);
        dump($this->my);

        dump($this);
    }
}
