<?php

namespace App\Controller;

use App\Entity\OAuthClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController {

    public function index(){

        return $this->render('account/index.html.twig',[
            "apps" => $this->getDoctrine()->getRepository(OAuthClient::class)->findAll(),
        ]);
    }

    public function permissions() {
        exit('permissions');
    }
}