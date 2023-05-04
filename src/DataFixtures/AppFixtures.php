<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\ArtworkProvider;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Artwork;
use App\Entity\Exhibition;
use App\Service\MySlugger;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;



class AppFixtures extends Fixture
{

    private $connection;
    private $slugger;
    // Injection od the DB Connection
    public function __construct(Connection $connection, MySlugger $slugger)
    {

        $this->connection = $connection;
        $this->slugger = $slugger;
    }

    /**
     * Reboot the id of  each table to 1
     */
    private function truncate()
    {

        // Checking of FK (foreign key) desactivated
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        // truncate of each table
        $this->connection->executeQuery('TRUNCATE TABLE user');
        $this->connection->executeQuery('TRUNCATE TABLE artwork');
        $this->connection->executeQuery('TRUNCATE TABLE exhibition');

        // Checking of FK (foreign key) reactivated
        $this->connection->executeQuery('SET foreign_key_checks = 1');
    }

    /**
     * Loading all fixtures
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        //Using the truncate method
        $this->truncate();
        // importing Faker
        $faker = Factory::create('fr_FR');
        // injection of a seed to keep the same data when reload the fixtures
        $faker->seed(22);

        //Our providers
        $faker->addProvider(new ArtworkProvider());


        //Creating users
        //Admin
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setPassword('$2y$13$Ov7uQzlJShWEfQoKfKiM8uCh0jQHhHU/XkfQ7J/4xroi5VcAEF7wu');
        $admin->setFirstname($faker->firstName());
        $admin->setLastname($faker->lastName());
        $admin->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);

        //Moderator
        $moderator = new User();
        $moderator->setEmail('moderator@moderator.com');
        $moderator->setPassword('$2y$13$/oyU5SMCpjo6Q1jXSV0D4OZT6i4kKyiIiRuf3jdxGDvr4d9P6bbwG');
        $moderator->setFirstname($faker->firstName());
        $moderator->setLastname($faker->lastName());
        $moderator->setRoles(["ROLE_MODERATOR"]);

        $manager->persist($moderator);

        //Artists

        //creation of an array to push each artist into it
        $artistList = [];

        // loop to create 8 artist
        for ($a = 1; $a < 8; $a++) {
            $artist = new User();
            $artist->setEmail($faker->email());
            $artist->setPassword('$2y$13$6M3bVDVn8BPs09HDqQyRh.wJYFxB1zVlzawxVDnwa3pUMr2cyGoNy');
            $artist->setFirstname($faker->firstName());
            $artist->setLastname($faker->lastName());
            $artist->setNickname($faker->userName());
            $artist->setDateOfBirth($faker->dateTimeBetween('-100 years', '- 10 years'));
            $artist->setPresentation($faker->paragraph());
            $artist->setAvatar('https://picsum.photos/id/' . $faker->numberBetween(1, 50) . '/50/50');
            $artist->setRoles(["ROLE_ARTIST"]);
            $fullname = $artist->getFirstname() . ' ' . $artist->getLastname();
            $slug = $this->slugger->slugify($fullname);
            $artist->setSlug($slug);


            $artistList[] = $artist;


            $manager->persist($artist);
        }

        //Exhibitions

        //creation of an array to push each exhibtion into it
        $exhibitionsList = [];

        for ($e = 1; $e <= 10; $e++) {
            $exhibition = new Exhibition();
            // unique exhibition
            $exhibition->setTitle($faker->word());
            $exhibition->setStartDate(new DateTime());
            $exhibition->setDescription($faker->paragraph());
            $exhibition->setEndDate(date_add(new DateTime(), date_interval_create_from_date_string("122 days")));
            $exhibition->setStatus('1');
            $slug = $this->slugger->slugify($exhibition->getTitle());
            $exhibition->setSlug($slug);

            // association of an artist to the exhibiton thanks to the artists array
            $randomArtist = $artistList[mt_rand(0, count($artistList) - 1)];
            $exhibition->setArtist($randomArtist);

            $exhibitionsList[] = $exhibition;

            $manager->persist($exhibition);
        }

        //Artworks

        for ($w = 1; $w <= 150; $w++) {
            $artwork = new Artwork();

            $artwork->setTitle($faker->word());
            $artwork->setDescription($faker->paragraph());
            $artwork->setPicture($faker->unique()->getArtwork());

            //association of an exhibition thanks to the exhibition array
            $randomExhibition = $exhibitionsList[mt_rand(0, count($exhibitionsList) - 1)];
            $artwork->setExhibition($randomExhibition);
            // think about change this when we will progress in our work for back experience
            $artwork->setStatus(1);
            $slug = $this->slugger->slugify($artwork->getTitle());
            $artwork->setSlug($slug);

            $manager->persist($artwork);
        }

        // push in database 
        $manager->flush();
    }
}
