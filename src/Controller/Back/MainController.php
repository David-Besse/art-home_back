<?php

namespace App\Controller\Back;

use App\Repository\ArtworkRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * Display the homepage of backoffice
     *
     * @Route("/", name="app_home_backoffice")
     */
    public function home(ArtworkRepository $artworkRepository)
    {
        $artworksNotValidate = $artworkRepository->findBy(['status' => false]);
        return $this->render('main.html.twig', ['artworksNotValidate' => $artworksNotValidate]);
    }
}
