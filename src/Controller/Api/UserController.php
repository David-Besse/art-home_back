<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Exhibition;
use App\Service\MySlugger;
use App\Repository\UserRepository;
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
     * Get informations from logged user
     *
     * @Route("api/secure/users/informations", name="app_api_users_informations", methods={"GET"})
     */
    // public function getInformationsFromUser()
    // {
       
    //     // getting the logged user
    //     /** @var \App\Entity\User $user */
    //     $user = $this->getUser();
        
    //     // setting an empty array
    //     $data = [];
  
    //     // setting a string depending on the role and return this string
    //     if(implode(',', $user->getRoles()) == 'ROLE_ARTIST')
    //     {
    //         $role = 'Artiste';
    //     }
    //     else if(implode(',', $user->getRoles()) == 'ROLE_ADMIN')
    //     {
    //         $role = 'Administrateur';
    //     }else 
    //     {
    //         $role = 'Modérateur';
    //     }

    //     if($user->getDateOfBirth() !== null){
    //         // modifying date format 
    //         $dateofBirth = date_format($user->getDateOfBirth(), 'd-m-Y');
    //     }else{
    //         $dateofBirth = $user->getDateOfBirth();
    //     }

    //     // putting the informations in the empty array
    //     $data = [
    //         'user' => $user,
    //         'role' => $role,
    //         'date' => $dateofBirth
    //     ];

        
    //     //sending the response with all data
    //     return $this->json(
    //         $data,
    //         Response::HTTP_OK,
    //         [],
    //         ['groups' => 'get_user']
    //     );
    // }
          
    /**
     * Get information artist and exhibitions for profile page
     * @Route("api/secure/users/profile", name="app_api_users_profile", methods={"GET"})
     */
    public function getInformationForProfile()
    {
        // getting the logged user
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // setting an empty array
        $data = [];

        // setting a string depending on the role and return this string
        if(implode(',', $user->getRoles()) == 'ROLE_ARTIST')
        {
            $role = 'Artiste';
        }
        else if(implode(',', $user->getRoles()) == 'ROLE_ADMIN')
        {
            $role = 'Administrateur';
        }else 
        {
            $role = 'Modérateur';
        }


        //fetching information about logged user
        $nickname = $user->getNickname();
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $email = $user->getEmail();

        if($user->getDateOfBirth() !== null){
            // modifying date format 
            $birthday = date_format($user->getDateOfBirth(), 'd-m-Y');
        }else{
            $birthday = $user->getDateOfBirth();
        }
        
        $avatar = $user->getAvatar();
        $presentation = $user->getPresentation();
        

        $exhibitionFetch = $user->getExhibition();
        $exhibitions = [];
        foreach ($exhibitionFetch as $exhibition){
            $id = $exhibition->getId();

            $title = $exhibition->getTitle();
            $exhibitions [] = [
                'id' => $id,
                'title' => $title
            ];

        }
        // putting the informations in the empty array
        $data = [
            'nickname' => $nickname,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'birthday' => $birthday,
            'avatar' => $avatar,
            'presentation' => $presentation,
            'role' => $role,
            'exhibitions' => $exhibitions

        ];
        

        //sending the response with all data
        return $this->json(
            $data,
            Response::HTTP_OK
            
        );
        
    }

    /**
     * Create a new user
     *
     * @param Request $request
     * @Route ("api/users/new", name="app_api_users_create", methods={"POST"})
     */
    public function createUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, MySlugger $slugger) : Response
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

        if ($user->getNickname() !== Null ) {

        }

        //Saving the entity and saving in DBB
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        //Return response if created
        return $this->json(
            [], 
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_user']
        );
    }
}
