<?php

namespace App\Controller\Back;

use App\Entity\Artwork;
use App\Form\ArtworkType;
use App\Repository\ArtworkRepository;
use App\Service\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/artwork")
 */
class ArtworkController extends AbstractController
{
    /**
     * @Route("/", name="app_artwork_index", methods={"GET"})
     */
    public function index(ArtworkRepository $artworkRepository): Response
    {
        return $this->render('artwork/index.html.twig', [
            'artworks' => $artworkRepository->findBy(['status' => true],['id' => 'DESC']),
        ]);
    }

    /**
     * Displaying artworks with status false
     *
     * @Route ("/validation-waiting", name="app_validation_waiting")
     */
    public function validatePage(ArtworkRepository $artworkRepository) : Response
    {
        return $this->render('artwork/validation.html.twig',
        [
            'artworks' => $artworkRepository->findBy(['status' => false])
        ]);
    }

    /**
     * @Route("/new", name="app_artwork_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ArtworkRepository $artworkRepository, MySlugger $slugger): Response
    {
        $artwork = new Artwork();
        $form = $this->createForm(ArtworkType::class, $artwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $slugger->slugify($artwork->getTitle());
            $artwork->setSlug($slug);
            $artworkRepository->add($artwork, true);

            $this->addFlash('warning', 'L\'oeuvre a été ajoutée et est en attente de validation');

            return $this->redirectToRoute('app_artwork_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('artwork/new.html.twig', [
            'artwork' => $artwork,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_artwork_show", methods={"GET"})
     */
    public function show(Artwork $artwork = null): Response
    {
        //404?
        if($artwork === null)
        {
            return $this->json(['error' => 'Oeuvre non trouvé.'], Response::HTTP_NOT_FOUND);
        }
        return $this->render('artwork/show.html.twig', [
            'artwork' => $artwork,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_artwork_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Artwork $artwork = null, ArtworkRepository $artworkRepository): Response
    {
        //404?
        if($artwork === null)
        {
            return $this->json(['error' => 'Oeuvre non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ArtworkType::class, $artwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $artworkRepository->add($artwork, true);

            $this->addFlash('success', 'L\'oeuvre a été modifiée');

            return $this->redirectToRoute('app_artwork_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('artwork/edit.html.twig', [
            'artwork' => $artwork,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_artwork_delete", methods={"POST"})
     */
    public function delete(Request $request, Artwork $artwork = null, ArtworkRepository $artworkRepository): Response
    {
        //404?
        if($artwork === null)
        {
            return $this->json(['error' => 'Oeuvre non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        if ($this->isCsrfTokenValid('delete'.$artwork->getId(), $request->request->get('_token'))) {
            $artworkRepository->remove($artwork, true);
        }

        $this->addFlash('danger', 'L\'oeuvre a été supprimée');
        return $this->redirectToRoute('app_artwork_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * validate an artwork
     * @Route("/artworks/{id}/validate", name ="app_artwork_validate", methods={"POST"})
     */
    public function validate(EntityManagerInterface $entityManager, Artwork $artwork = null) : Response
    {
        //404?
        if($artwork === null)
        {
            return $this->json(['error' => 'Oeuvre non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        
        $artwork->setStatus(1);
        $entityManager->persist($artwork);
        $entityManager->flush();

        $this->addFlash('success', 'L\'oeuvre a été validée');
        return $this->redirectToRoute('app_validation_waiting');
    }
}
