<?php

namespace App\Controller\Api;

use App\Entity\Artwork;
use App\Entity\Exhibition;
use App\Repository\ArtworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;


class ArtworkController extends AbstractController
{
    /**
     * Get all artworks entity
     * @Route("/api/artworks", name="app_api_artwork", methods={"GET"})
     */
    public function getArtworks(ArtworkRepository $artworkRepository): Response
    {
        // fetch all artworks
        $artworks = $artworkRepository->findAll();
   
        // transform data in json format
        return $this->json(
            $artworks, 
            Response::HTTP_OK,
            [],
            ['groups' => 'get_artworks_collection']
        );
    }

    /**
     * Get on artwork entity
     *
     * @Route("/api/artworks/{id}", name="app_api_artwork_by_id", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function getArtworkById(Artwork $artwork): Response
    {

        // transform entity Artwork into json 
        return $this->json(
            $artwork,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_artwork']
        );

    }

    /**
     * Create an artwork entity
     *
     * @Route("/api/artworks/new", name="app_api_artwork_new", methods={"POST"})
     */
    public function createArtwork(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer, ValidatorInterface $validator) : Response
    {
        //Fetch the json content
        $jsonContent = $request->getContent();

        
        // Checking if json format is respected
        //if not, throw an error
        try{
            //Transforming json Content into entity
            $artwork = $serializer->deserialize($jsonContent, Artwork::class, 'json');
            

        }catch(NotEncodableValueException $e) {

            return $this->json(
                ['error' => 'JSON INVALIDE'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Checking the entity : if all fields are well fill
        
        $errors = $validator->validate($artwork);

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

        //Saving the entity and saving in DBB
        $entityManager = $doctrine->getManager();
        $entityManager->persist($artwork);
        $entityManager->flush();

        //Return response if created
        return $this->json(
            $artwork, 
            Response::HTTP_CREATED,
            [],
            ['groups' => 'get_artwork']
        );
    }
    
    /**
     * Edit artwork entity
     *
     * @Route("api/artworks/{id}/edit", name="app_api_artwork_edit", requirements={"id"="\d+"}, methods={"PUT"})
     */
    public function editArtwork(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer, ValidatorInterface $validator, Artwork $artworkToEdit) : Response
    {

         //Fetch the json content
         $jsonContent = $request->getContent();

        
         // Checking if json format is respected
         //if not, throw an error
         try{
             //Transforming json Content into entity
             $artwork = $serializer->deserialize($jsonContent, Artwork::class, 'json');
 
         }catch(NotEncodableValueException $e) {
 
             return $this->json(
                 ['error' => 'JSON INVALIDE'],
                 Response::HTTP_UNPROCESSABLE_ENTITY
             );
         }
 
         // Checking the entity : if all fields are well fill
         
         $errors = $validator->validate($artwork);
 
         //Checking if there is any error
         // If yes, then throw an error
         if (count($errors) > 0) {
            // return array
            $errorsClean = [];
            // Clean error messages
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
 
         
         //Saving the entity and saving in DBB
         $entityManager = $doctrine->getManager();

         $artworkToEdit->setTitle($artwork->getTitle());
         $artworkToEdit->setDescription($artwork->getDescription());
         $artworkToEdit->setPicture($artwork->getPicture());
         $artworkToEdit->setExhibition($artwork->getExhibition());

         $entityManager->persist($artworkToEdit);
         $entityManager->flush();
 
         //Return response if created
         return $this->json(
             $artwork, 
             Response::HTTP_OK,
             [],
             ['groups' => 'get_artwork']
         );
        
    }

    /**
     * Remove an entity
     *
     * @Route("api/artworks/{id}/delete", name="app_api_artwork_delete",requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function deleteArtwork(Artwork $artwork = null, EntityManagerInterface $entityManager) : Response
    {

        if($artwork === null)
        {
            return $this->json(['error' => 'Film non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // remove entity artwork
        $entityManager->remove($artwork);
        $entityManager->flush();

        // return status 200
        return $this->json(
            [],
            Response::HTTP_NO_CONTENT
        );
        
    }

    /**
     * Get artworks by exhibition for profile page
     * @Route("api/artworks/exhibitions/{id}/profile", name="app_api_artwork_profile",requirements={"id"="\d+"}, methods={"GET"})
     */
    public function getArtworksByExhibition(Exhibition $exhibition, ArtworkRepository $artworkRepository)
    {
        $artworksList = $artworkRepository->findArtworksByExhibitionForProfilePageQB($exhibition);

        return $this->json($artworksList, Response::HTTP_OK, [], ['groups' => 'get_artwork_by_exhibition'] );
    }


}
