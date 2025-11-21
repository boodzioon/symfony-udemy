<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Author;
use App\Entity\Pdf;
use App\Entity\Video;

class InheritanceEntitiesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i<=2; $i++) {
            $author = new Author();
            $author->setName('Author ' . $i);
            $manager->persist($author);

            for ($j=1; $j<=3; $j++) {
                $pdf = new Pdf();
                $pdf->setPagesNumber(rand(100, 200))
                    ->setOrientation('portrait')
                    ->setFilename('pdf-' . $j . '-' . $i)
                    ->setDescription('Pdf ' . $j . ' of Author ' . $i)
                    ->setSize(rand(5000, 50000))
                    ->setAuthor($author);
                $manager->persist($pdf);
            }

            for ($k=1; $k<=3; $k++) {
                $video = new Video();
                $video->setDuration(rand(300, 1200))
                    ->setFormat('mpeg')
                    ->setFilename('video-' . $k . '-' . $i)
                    ->setDescription('Video ' . $k . ' of Author ' . $i)
                    ->setSize(rand(250000, 5000000))
                    ->setAuthor($author);
                $manager->persist($video);
            }
        }

        $manager->flush();
    }
}
