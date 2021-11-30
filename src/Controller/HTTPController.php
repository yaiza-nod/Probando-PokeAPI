<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HTTPController extends AbstractController
{
    /**
     * @Route("/h/t/t/p", name="h_t_t_p")
     */
    public function index(): Response
    {
        return $this->render('http/index.html.twig', [
            'controller_name' => 'HTTPController',
        ]);
    }
}
