<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmailsTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $client->enableProfiler();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello DEFAULTCONTROLLER!');

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        $this->assertInstanceOf('Swift_Message', $message);

        $this->assertSame('Hello Email!', $message->getSubject());
        $this->assertSame('boodzioo_n@o2.pl', key($message->getFrom()));
        $this->assertSame('boodzioo_n@o2.pl', key($message->getTo()));
        $this->assertStringContainsString("You registered", $message->getBody());
    }
}
