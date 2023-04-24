<?php

namespace App\Controller\Back;

use App\Entity\Exhibition;
use App\Form\ExhibitionType;
use App\Repository\ExhibitionRepository;
use App\Service\MySlugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/exhibition")
 */
class ExhibitionController extends AbstractController
{
    /**
     * Display all exhibitions
     * 
     * @Route("/", name="app_exhibition_index", methods={"GET"})
     */
    public function index(ExhibitionRepository $exhibitionRepository): Response
    {
        return $this->render('exhibition/index.html.twig', [
            'exhibitions' => $exhibitionRepository->findBy([], ['title' => 'ASC']),
        ]);
    }

    /**
     * Display add form and process form
     * 
     * @Route("/new", name="app_exhibition_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ExhibitionRepository $exhibitionRepository, MySlugger $slugger): Response
    {
        // create exhibition entity and create new form
        $exhibition = new Exhibition();
        $form = $this->createForm(ExhibitionType::class, $exhibition);
        $form->handleRequest($request);

        //if form is submited
        if ($form->isSubmitted() && $form->isValid()) {

            //slugify the title
            $slug = $slugger->slugify($exhibition->getTitle());
            $exhibition->setSlug($slug);
            $exhibitionRepository->add($exhibition, true);

            // flash messages
            $this->addFlash('success', 'L\'exposition a été ajoutée');

            //redirection
            return $this->redirectToRoute('app_exhibition_index', [], Response::HTTP_SEE_OTHER);
        }

        //else return twig and display new form
        return $this->renderForm('exhibition/new.html.twig', [
            'exhibition' => $exhibition,
            'form' => $form,
        ]);
    }

    /**
     * Display exhibition entity
     * 
     * @Route("/{id}", name="app_exhibition_show", methods={"GET"})
     */
    public function show(Exhibition $exhibition = null): Response
    {
        //404?
        if ($exhibition === null) {
            return $this->json(['error' => 'Exposition non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        return $this->render('exhibition/show.html.twig', [
            'exhibition' => $exhibition,
        ]);
    }

    /**
     * Display edit form and process edit form
     * 
     * @Route("/{id}/edit", name="app_exhibition_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Exhibition $exhibition = null, ExhibitionRepository $exhibitionRepository): Response
    {

        //404?
        if ($exhibition === null) {
            return $this->json(['error' => 'Exposition non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        //create edit form
        $form = $this->createForm(ExhibitionType::class, $exhibition);
        $form->handleRequest($request);

        //if form is submited
        if ($form->isSubmitted() && $form->isValid()) {
            $exhibitionRepository->add($exhibition, true);

            //flash messages
            $this->addFlash('success', 'L\'exposition a été modifiée');

            //redirection
            return $this->redirectToRoute('app_exhibition_index', [], Response::HTTP_SEE_OTHER);
        }

        //else return twig and edit form
        return $this->renderForm('exhibition/edit.html.twig', [
            'exhibition' => $exhibition,
            'form' => $form,
        ]);
    }

    /**
     * Delete an exhibition item
     * 
     * @Route("/{id}", name="app_exhibition_delete", methods={"POST"})
     */
    public function delete(Request $request, Exhibition $exhibition = null, ExhibitionRepository $exhibitionRepository): Response
    {
        //404?
        if ($exhibition === null) {
            return $this->json(['error' => 'Exposition non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // if token is valid
        if ($this->isCsrfTokenValid('delete' . $exhibition->getId(), $request->request->get('_token'))) {

            //then remove item
            $exhibitionRepository->remove($exhibition, true);
        }

        // flash messages
        $this->addFlash('danger', 'L\'exposition a été supprimée');

        //redirection
        return $this->redirectToRoute('app_exhibition_index', [], Response::HTTP_SEE_OTHER);
    }
}
