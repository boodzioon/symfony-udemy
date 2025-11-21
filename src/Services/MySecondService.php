<?php

namespace App\Services;

class MySecondService implements ServiceInterface
{

    public function __construct()
    {
        dump('Second Service');
    }

    public function doSomething()
    {
        dump('Do something');
    }
}
