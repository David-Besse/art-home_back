<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * Get all artworks from logged user
     * 
     * @Route("api/users/exhibitions", name="app_api_users_exhibitions", methods={"GET"})
     */
    public function getArtworksFromUser()
    {
        // getting the logged user
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        //fetching exhibitons depending on the user
        $exhibitions = $user->getExhibition();

        //returning response with data
        return $this->json(
            $exhibitions,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_exhibitions_collection']
        );
    }

    /**
     * Get informations from logged user
     *
     * @Route("api/users/informations", name="app_api_users_informations", methods={"GET"})
     */
    public function getInformationsFromUser()
    {
        // getting the logged user
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // setting an empty array
        $data = [];

        //fetching information about logged user
        $nickname = $user->getNickname();
        $roles = $user->getRoles();
        
        // setting a string depending on the role and return this string
        if(implode(',', $roles) == 'ROLE_ARTIST')
        {
            $role = 'Artiste';
        }
        else if(implode(',', $roles) == 'ROLE_ADMIN')
        {
            $role = 'Administrateur';
        }else 
        {
            $role = 'Modérateur';
        }

        // putting the informations in the empty array
        $data = [
            'nickname' => $nickname,
            'roles' => $role
        ];

        //sending the response with all data
        return $this->json(
            $data,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_user_data']
        );
    }

    /**
     * Create a new user
     *
     * @param Request $request
     * @Route ("api/users/new", name="app_api_users_create", methods={"POST"})
     */
    public function createUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher) : Response
    {

        //Fetch the json content
        $jsonContent = $request->getContent();

        
        // Checking if json format is respected
        //if not, throw an error
        try{
            //Transforming json Content into entity
            $user = $serializer->deserialize($jsonContent, User::class, 'json');
            

        }catch(NotEncodableValueException $e) {

            return $this->json(
                ['error' => 'JSON INVALIDE'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Checking the entity : if all fields are well fill
        
        $errors = $validator->validate($user);

        //Checking if there is any error
        // If yes, then throw an error
        if (count($errors) > 0) {
            // return array
            $errorsClean = [];
            // @Clean error messages
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //hashing the password
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

        //Saving the entity and saving in DBB
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        //Return response if created
        return $this->json(
            $user, 
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_user']
        );
    }
}
