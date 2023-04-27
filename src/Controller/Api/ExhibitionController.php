<?php

namespace App\Controller\Api;

use App\Entity\Exhibition;
use App\Entity\User;
use App\Repository\ArtworkRepository;
use App\Repository\ExhibitionRepository;
use App\Service\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
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
     *@Route("/api/exhibitions", name="app_api_exhibitions_get", methods={"GET"})
     */
    public function getExhibitions(ExhibitionRepository $exhibitionRepository): Response
    {
        //fetching all exhibitions
        $exhibitionsList = $exhibitionRepository->findAll();

        // return status 200
        return $this->json($exhibitionsList, Response::HTTP_OK, [], ['groups' => 'get_exhibitions_collection']);
    }

    /**
     * Get one exhibition and related artworks and related artist
     * @Route("/api/exhibitions/{id<\d+>}", name="app_api_exhibition_by_id", methods={"GET"})
     */
    public function getExhibitionById(Exhibition $exhibition = null, ArtworkRepository $artworkRepository): Response
    {

        // 404 ?
        if ($exhibition === null) {
            return $this->json(['error' => 'Exposition non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        //ftech the artist
        $artist = $exhibition->getArtist();

        //fetch artworks with status true
        $artworks = $artworkRepository->findBy(['status' => 1, 'exhibition' => $exhibition]);

        //put data in array
        $data[] = [
            'exhibition' => $exhibition,
            'artwork' => $artworks,
            'artist' => $artist
        ];

        // return status 200
        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'get_exhibition_artwork_artist_by_id']);
    }

    /**
     * Create exhibition item
     * @Route("/api/secure/exhibitions/new", name="app_api_exhibition_new", methods={"POST"})
     */
    public function createExhibition(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

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

        //setting the artist thanks to logged user
        $exhibition->setArtist($user);

        // Save entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($exhibition);
        $entityManager->flush();

        // return status 201
        return $this->json(
            $user->getExhibition(),
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_exhibitions_collection']
        );
    }

    /**
     * Edit exhibition item
     * @Route("/api/secure/exhibitions/{id<\d+>}/edit", name="app_api_exhibition_edit", methods={"PATCH"})
     */
    public function editExhibition(Exhibition $exhibitionToEdit = null, Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        // 404 ?
        if ($exhibitionToEdit === null) {
            return $this->json(['error' => 'Exposition non trouvé.'], Response::HTTP_NOT_FOUND);
        }


        //Get Json content
        $jsonContent = $request->getContent();

        try {
            // Convert Json in doctrine entity
            $exhibitionModified = $serializer->deserialize($jsonContent, Exhibition::class, 'json', ['object_to_populate' => $exhibitionToEdit]);
        } catch (NotEncodableValueException $e) {
            // if json getted isn't right, make an alert for client
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        //Validate entity
        $errors = $validator->validate($exhibitionModified);

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
        $entityManager->persist($exhibitionModified);
        $entityManager->flush();

        // return status 200
        return $this->json(
            $exhibitionModified,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_exhibition_by_id']
        );
    }

    /**
     * Delete an exhibition item
     * @Route("/api/secure/exhibitions/{id<\d+>}/delete", name="app_api_exhibition_delete", methods={"DELETE"})
     */
    public function deleteExhibition(Exhibition $exhibitionToDelete = null, EntityManagerInterface $entityManager): Response
    {

        // 404 ?
        if ($exhibitionToDelete === null) {
            return $this->json(['error' => 'Exposition non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // remove entity from DB
        $entityManager->remove($exhibitionToDelete);
        $entityManager->flush();

        //return status 204
        return $this->json(
            [],
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     * Get exhibitions infos and principal picture for homepage
     * @Route("api/exhibitions/homepage", name="app_api_exhibitions_homepage", methods={"GET"})
     */
    public function getExhibitionsForHomepage(ExhibitionRepository $exhibitionRepository): Response
    {
        //fetching exhibitons for homepage
        $exhibitionsList = $exhibitionRepository->findAllForHomeSQL();

        //return status 200
        return $this->json($exhibitionsList, Response::HTTP_OK, [], ['groups' => 'get_exhibitions_collection']);
    }

    /**
     * Get active exhibitions infos by artist to submit artwork form
     * @Route("api/exhibitions/artist/{id<\d+>}/form", name="app_api_exhibitions_artist_form", methods={"GET"})
     */
    public function getActiveExhibitionsForArtworkForm(ExhibitionRepository $exhibitionRepository, User $artist = null): Response
    {
        // 404 ?
        if ($artist === null) {
            return $this->json(['error' => 'Artiste non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        //fecthing id and title of exhibitions for submit artwork form
        $exhibitionsList = $exhibitionRepository->findTitleAndIdForFormSQL($artist);

        // return status 200
        return $this->json($exhibitionsList, Response::HTTP_OK, [], ['groups' => 'get_exhibitions_collection']);
    }
}
