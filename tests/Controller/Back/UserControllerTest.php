<?php

namespace App\Tests\Controller\Back;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * Check if moderator can access UserController paths
     *
     */
    public function testModeratorAccess(): void
    {
        //create a user
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('moderator@moderator.com');
        //connecting as user
        $client->loginUser($testUser);
        //route to test
        $crawler = $client->request('GET', 'user/');

        //status 403 if forbidden access
        $this->assertResponseStatusCodeSame(403);
    }
}
