<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerLinkTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/home');
        $link = $crawler->filter('a:contains("Login")')->link();

        $crawler = $client->click($link);
        $this->assertStringContainsString('Remember me', $client->getResponse()->getContent());
    }
}
