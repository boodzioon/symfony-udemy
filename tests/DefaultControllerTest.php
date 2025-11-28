<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/home');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello');

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Hello DEFAULTCONTROLLER")')->count()
        );

        $this->assertLessThan(
            1,
            $crawler->filter('h1.main_header')->count()
        );

        $this->assertCount(1, $crawler->filter('h1'));

        $this->assertFalse(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'The "Content-Type" header is "application/json"'
        );
        $this->assertStringContainsString('This friendly message is coming from', $client->getResponse()->getContent());
        $this->assertRegExp('/GAcode-(123)?/', $client->getResponse()->getContent(), 'Content contains RegExp"foo(bar)"');
    }
}
