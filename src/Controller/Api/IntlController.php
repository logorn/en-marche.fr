<?php

namespace AppBundle\Controller\Api;

use AppBundle\Intl\FranceCitiesBundle;
use AppBundle\Intl\VoteOfficeBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class IntlController extends Controller
{
    /**
     * @Route("/postal-code/{postalCode}", name="api_postal_code", methods={"GET"})
     */
    public function postalCodeAction(string $postalCode): JsonResponse
    {
        return new JsonResponse(FranceCitiesBundle::getPostalCodeCities($postalCode));
    }

    /**
     * @Route("/vote-offices/{countryCode}", name="api_vote_offices", methods={"GET"})
     */
    public function voteOfficesAction(string $countryCode): JsonResponse
    {
        return new JsonResponse(VoteOfficeBundle::getVoteOfficies($countryCode));
    }
}
