<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
}
