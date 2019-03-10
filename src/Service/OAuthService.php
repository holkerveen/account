<?php

namespace App\Service;

class OAuthService
{

    public function validateAuthorizationRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $authRequest = $server->validateAuthorizationRequest($request);
//        /** @var UserEntityInterface $user */
//        $user = $security->getUser();
//        $authRequest->setUser($user);
//        $this->validateRequest($request);
        var_dump('hee');
        exit('');
    }
}