<?php

namespace App\Tests\Controller\Back;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * Check if moderator can access UserController paths
     *
     * @return void
     */
    public function testModeratorAccess(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('moderator@moderator.com');
        $client->loginUser($testUser);
        $crawler = $client->request('GET','user/' );

        $this->assertResponseStatusCodeSame(403);
    }
}
