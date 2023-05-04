<?php

namespace App\Service;

use App\Repository\ArtworkRepository;

/**
 * Checking if there is an artwork with status false
 */
class ArtworkValidateLayout
{
    private $artworkRepository;

    public function __construct(ArtworkRepository $artworkRepository)
    {
        $this->artworkRepository = $artworkRepository;
    }

    public function checkIfArtworkToValidate()
    {
        $artworksToValidate = $this->artworkRepository->findBy(['status' => false]);

        return $artworksToValidate;
    }
}
