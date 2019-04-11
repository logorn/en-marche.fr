<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HealthController extends Controller
{
    /**
     * @Route("/health", name="health", methods={"GET"})
     */
    public function healthAction()
    {
        return new Response('Healthy');
    }
}
