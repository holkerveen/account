<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/app/new", name="app.new")
     */
    public function index()
    {
        return $this->render('app/new.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}
