<?php

namespace App\Tests\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    public function testGetInformationForProfile(): void
    {

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('gilles.margaret@chevalier.com');
        // simulate $testUser being logged in
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/api/secure/users/profile');

        $this->assertResponseIsSuccessful();
        
    }

     /**
     * Test route for create an user
     
     */
    public function testCreateUser(): void
    {
        $client = static::createClient();

        $email = 'jjon@gmail.com';
        $password = 'jean';
        $lastname = 'louis';
        $firstname = 'jean';
        $roles = ['ROLE_ARTIST'];

        $data = [
            'email' => $email,
            'password' => $password,
            'lastname' => $lastname,
            'firstname' => $firstname,
            'roles' => $roles
        ];
        
        $crawler = $client->request('POST', '/api/users/new',[],[],[], json_encode($data));

        $this->assertResponseStatusCodeSame(201);
        
    }

}
