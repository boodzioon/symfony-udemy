<?php

namespace App\Tests;

use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerIsolatedTest extends WebTestCase
{

    private $em;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    protected function tearDown(): void
    {
        $this->em->rollback();
        $this->em->close();
        $this->em = null;
    }

    public function testSomething(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // $video = $this->em->getRepository(Video::class)->find(1);
        // $this->em->remove($video);
        // $this->em->flush();

        $video = new Video;
        $video->setFilename('Video Path');
        $video->setDescription('Video Description');
        $video->setCreatedAt(new \DateTime());
        $video->setSize(532511);
        $video->setDuration(157);
        $video->setFormat('mpeg');
        $this->em->persist($video);
        $this->em->flush();

        $this->assertTrue($video->getId() > 0);
    }
}
