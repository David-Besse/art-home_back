<?php

namespace App\Controller\Api;

use App\Entity\Exhibition;
use App\Repository\ExhibitionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ExhibitionController extends AbstractController
{
    /**
     * Get all exhibitions 
     *@Route("/api/exhibitions", name="api_exhibitions_get", methods={"GET"})
     */
    public function getExhibitions(ExhibitionRepository $exhibitionRepository): Response
    {
        $exhibitionsList = $exhibitionRepository->findAll();

        return $this->json($exhibitionsList, Response::HTTP_OK, [], ['groups' => 'get_exhibitions_collection']);
    }

    /**
     * Get one exhibition
     * @Route("/api/exhibitions/{id<\d+>}", name="api_exhibition_by_id", methods={"GET"})
     */
    public function getExhibitionById(Exhibition $exhibition): Response
    {
        

        return $this->json($exhibition, Response::HTTP_OK, [], ['groups' => 'get_exhibition_by_id']);
    }

    /**
     * Create  exhibition item
     * @Route("/api/exhibitions/new", name="api_exhibition_new", methods={"PUT"})
     */
    public function createExhibition(Request $request, SerializerInterface $serializer,ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
       //Get Json content
       $jsonContent = $request->getContent();

       try {
            // Convert Json in doctrine entity
            $exhibition = $serializer->deserialize($jsonContent, Exhibition::class, 'json');
       } catch (NotEncodableValueException $e) {
            // if json getted isn't right, make an alert for client
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
       }

       //Validate entity
       $errors = $validator->validate($exhibition);

       // Is there some errors ?
       if (count($errors) > 0) {
           //returned array
           $errorsClean = [];
           // @get back validation errors clean
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);

       }

       // Save entity
       $entityManager = $doctrine->getManager();
       $entityManager->persist($exhibition);
       $entityManager->flush();

       // On retourne la réponse adaptée (201 + Location: URL de la ressource)
       return $this->json(
        // Le film créé peut être ajouté au retour
        $exhibition,
        // Le status code : 201 CREATED
        // utilisons les constantes de classes !
        Response::HTTP_CREATED,
        // REST demande un header Location + URL de la ressource
        [
            // Nom de l'en-tête + URL
            'Location' => $this->generateUrl('api_exhibition_by_id', ['id' => $exhibition->getId()])
        ],
        // Groups
        ['groups' => 'get_exhibition_by_id']
    );
    }
}