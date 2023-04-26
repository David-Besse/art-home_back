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
     * Displaying all validate artworks
     * 
     * @Route("/", name="app_artwork_index", methods={"GET"})
     */
    public function index(ArtworkRepository $artworkRepository): Response
    {
        return $this->render('artwork/index.html.twig', [
            'artworks' => $artworkRepository->findBy(['status' => true], ['id' => 'DESC']),
        ]);
    }

    /**
     * Displaying artworks with status false
     *
     * @Route ("/validation-waiting", name="app_validation_waiting", methods={"GET"})
     */
    public function validatePage(ArtworkRepository $artworkRepository): Response
    {
        return $this->render('artwork/validation.html.twig', [
            'artworks' => $artworkRepository->findBy(['status' => false])
        ]);
    }

    /**
     * Display create form and form process
     * 
     * @Route("/new", name="app_artwork_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ArtworkRepository $artworkRepository): Response
    {
        // create a new artwork entity and form
        $artwork = new Artwork();
        $form = $this->createForm(ArtworkType::class, $artwork);
        $form->handleRequest($request);

        // if form is submitted
        if ($form->isSubmitted() && $form->isValid()) {

            $artworkRepository->add($artwork, true);

            // add flash messages
            $this->addFlash('warning', 'L\'oeuvre a été ajoutée et est en attente de validation');

            // redirection
            return $this->redirectToRoute('app_artwork_index', [], Response::HTTP_SEE_OTHER);
        }

        //else return twig with the form
        return $this->renderForm('artwork/new.html.twig', [
            'artwork' => $artwork,
            'form' => $form,
        ]);
    }

    /**
     * Display an artwork
     * 
     * @Route("/{id}", name="app_artwork_show", methods={"GET"})
     */
    public function show(Artwork $artwork = null): Response
    {
        //404?
        if ($artwork === null) {
            return $this->json(['error' => 'Oeuvre non trouvé.'], Response::HTTP_NOT_FOUND);
        }
        return $this->render('artwork/show.html.twig', [
            'artwork' => $artwork,
        ]);
    }

    /**
     * Display edit form and form process
     * 
     * @Route("/{id}/edit", name="app_artwork_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Artwork $artwork = null, ArtworkRepository $artworkRepository): Response
    {
        //404?
        if ($artwork === null) {
            return $this->json(['error' => 'Oeuvre non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // create edit form
        $form = $this->createForm(ArtworkType::class, $artwork);
        $form->handleRequest($request);

        //if form is submited
        if ($form->isSubmitted() && $form->isValid()) {

            $artworkRepository->add($artwork, true);
            
            //adding flash messages
            $this->addFlash('success', 'L\'oeuvre a été modifiée');

            //redirection
            return $this->redirectToRoute('app_artwork_index', [], Response::HTTP_SEE_OTHER);
        }

        //else return twig with edit form
        return $this->renderForm('artwork/edit.html.twig', [
            'artwork' => $artwork,
            'form' => $form,
        ]);
    }

    /**
     * Process delete form
     * 
     * @Route("/{id}", name="app_artwork_delete", methods={"POST"})
     */
    public function delete(Request $request, Artwork $artwork = null, ArtworkRepository $artworkRepository): Response
    {
        //404?
        if ($artwork === null) {
            return $this->json(['error' => 'Oeuvre non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // if token is valid
        if ($this->isCsrfTokenValid('delete' . $artwork->getId(), $request->request->get('_token'))) {

            //removing the entity
            $artworkRepository->remove($artwork, true);
        }

        // flash messages
        $this->addFlash('danger', 'L\'oeuvre a été supprimée');

        //redirection
        return $this->redirectToRoute('app_artwork_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Validate an artwork
     * 
     * @Route("/artworks/{id}/validate", name ="app_artwork_validate", methods={"POST"})
     */
    public function validate(EntityManagerInterface $entityManager, Artwork $artwork = null): Response
    {
        //404?
        if ($artwork === null) {
            return $this->json(['error' => 'Oeuvre non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        //if form is submited
        //ten set new status to true
        $artwork->setStatus(1);
        $entityManager->persist($artwork);
        $entityManager->flush();

        //flash messages
        $this->addFlash('success', 'L\'oeuvre a été validée');

        //redirection
        return $this->redirectToRoute('app_validation_waiting');
    }
}
