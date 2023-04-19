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
     * @Route("/", name="app_exhibition_index", methods={"GET"})
     */
    public function index(ExhibitionRepository $exhibitionRepository): Response
    {
        return $this->render('exhibition/index.html.twig', [
            'exhibitions' => $exhibitionRepository->findBy([],['title' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="app_exhibition_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ExhibitionRepository $exhibitionRepository, MySlugger $slugger): Response
    {
        $exhibition = new Exhibition();
        $form = $this->createForm(ExhibitionType::class, $exhibition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $slugger->slugify($exhibition->getTitle());
            $exhibition->setSlug($slug);
            $exhibitionRepository->add($exhibition, true);
            $this->addFlash('success', 'L\'exposition a été ajoutée');

            return $this->redirectToRoute('app_exhibition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exhibition/new.html.twig', [
            'exhibition' => $exhibition,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_exhibition_show", methods={"GET"})
     */
    public function show(Exhibition $exhibition = null): Response
    {
        //404?
        if($exhibition === null)
        {
            return $this->json(['error' => 'Exposition non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        return $this->render('exhibition/show.html.twig', [
            'exhibition' => $exhibition,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_exhibition_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Exhibition $exhibition = null, ExhibitionRepository $exhibitionRepository): Response
    {

        //404?
        if($exhibition === null)
        {
            return $this->json(['error' => 'Exposition non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ExhibitionType::class, $exhibition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exhibitionRepository->add($exhibition, true);

            $this->addFlash('success', 'L\'exposition a été modifiée');

            return $this->redirectToRoute('app_exhibition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exhibition/edit.html.twig', [
            'exhibition' => $exhibition,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_exhibition_delete", methods={"POST"})
     */
    public function delete(Request $request, Exhibition $exhibition = null, ExhibitionRepository $exhibitionRepository): Response
    {
        //404?
        if($exhibition === null)
        {
            return $this->json(['error' => 'Exposition non trouvé.'], Response::HTTP_NOT_FOUND);
        }
        
        if ($this->isCsrfTokenValid('delete'.$exhibition->getId(), $request->request->get('_token'))) {
            $exhibitionRepository->remove($exhibition, true);
        }

        $this->addFlash('danger', 'L\'exposition a été supprimée');

        return $this->redirectToRoute('app_exhibition_index', [], Response::HTTP_SEE_OTHER);
    }
}
