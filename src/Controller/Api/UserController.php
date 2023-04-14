<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    // /** @var \App\Entity\User $user */
    // $user = $this->getUser();
    // dd($user); 
    /**
     * Get all artworks from logged user
     * 
     * @Route("api/users/exhibitions", name="app_api_users_exhibitions", methods={"GET"})
     */
    public function getArtworksFromUser()
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $exhibitions = $user->getExhibition();
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
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $data = [];
        $nickname = $user->getNickname();
        $roles = $user->getRoles();
        
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

        $data = [
            'nickname' => $nickname,
            'roles' => $role
        ];

        return $this->json(
            $data,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_user_data']
        );
    }
}
