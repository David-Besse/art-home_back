<?php

namespace App\DataFixtures;

use App\Entity\Artwork;
use App\Entity\Exhibition;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(22);
        

        //Users
            //admin
            $admin = new User();
            $admin->setEmail('admin@admin.com');
            $admin->setPassword('$2y$13$Ov7uQzlJShWEfQoKfKiM8uCh0jQHhHU/XkfQ7J/4xroi5VcAEF7wu');
            $admin->setFirstname($faker->firstName());
            $admin->setLastname($faker->lastName());
            $admin->setRoles(["ROLE_ADMIN"]);
            
            $manager->persist($admin);

            //moderator
            $moderator =new User();
            $moderator->setEmail('moderator@moderator.com');
            $moderator->setPassword('$2y$13$/oyU5SMCpjo6Q1jXSV0D4OZT6i4kKyiIiRuf3jdxGDvr4d9P6bbwG');
            $moderator->setFirstname($faker->firstName());
            $moderator->setLastname($faker->lastName());
            $moderator->setRoles(["ROLE_MODERATOR"]);

            $manager->persist($moderator);

            //artist

            $artistList = []; 
            for($a = 1 ; $a < 8; $a++ )
            {
                $artist = new User();
                $artist->setEmail($faker->email());
                $artist->setPassword('$2y$13$6M3bVDVn8BPs09HDqQyRh.wJYFxB1zVlzawxVDnwa3pUMr2cyGoNy');
                $artist->setFirstname($faker->firstName());
                $artist->setLastname($faker->lastName());
                $artist->setNickname($faker->userName());
                $artist->setAvatar('https://picsum.photos/id/' . $faker->numberBetween(1, 50) . '/50/50');
                $artist->setRoles(["ROLE_ARTIST"]);

                $artistList[] = $artist;


                $manager->persist($artist);
            }

        //Exhibitions
        $exhibitionsList = [];

        for ($e = 1; $e<=10; $e++) {
            $exhibition = new Exhibition();
            // unique exhib.
            $exhibition->setTitle($faker->word());
            $exhibition->setStartDate($faker->dateTimeBetween('-1 week'));
            
            $exhibition->setEndDate(date_modify($exhibition->getStartDate(),'+4 month'));
            // slug method to verify
            // $exhibition->setSlug($exhibition->getTitle());
            $exhibition->setStatus('1');

            for ($g = 1; $g <= mt_rand(1, 2); $g++) {
                $randomArtist = $artistList[mt_rand(0, count($artistList) - 1)];
                $exhibition->setArtist($randomArtist);
            }
            
            $exhibitionsList[] = $exhibition;
            
            $manager->persist($exhibition);
            
        }
        
        //Artworks

        for ($w = 1; $w<=150; $w++) {
            $artwork = new Artwork();
            
            $artwork->setTitle($faker->word());
            $artwork->setDescription($faker->paragraph());
            $artwork->setPicture('https://picsum.photos/id/' . $faker->numberBetween(1, 300) . '/300/300');

            $randomExhibition = $exhibitionsList[mt_rand(0, count($exhibitionsList) - 1)];
            $artwork->setExhibition($randomExhibition);
            // think about change this when we will progress in our work for back experience
            $artwork->setStatus('1');

            $manager->persist($artwork);
        }

        $manager->flush();
    }
}
