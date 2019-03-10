<?php

namespace App\Controller;

use App\Entity\OAuthClient;
use App\Entity\OAuthGrantedScope;
use App\Entity\User;
use Exception;
use League\OAuth2\Server\AuthorizationServer;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Zend\Diactoros\ServerRequestFactory;


class OAuthController extends AbstractController
{

    public function __construct()
    {

    }

    /**
     * @Route("/authorize", name="authorize", methods={"GET"})
     * @param Request $request
     * @param AuthorizationServer $server
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function authorize(Request $request, AuthorizationServer $server, Session $session)
    {

        $psrRequest = (new DiactorosFactory())->createRequest($request);

        $authorizationRequest = $server->validateAuthorizationRequest($psrRequest);

        $authorizationRequest->setUser($this->getUser());

        $session->set('authorizationRequest', $authorizationRequest);

        return $this->render('oauth/scopes.html.twig', [
            'client' => $authorizationRequest->getClient(),
            'scopes' => $authorizationRequest->getScopes(),
        ]);
    }

    /**
     * @Route("/authorize", name="authorizeUserResponse", methods={"POST"})
     * @param Request $request
     * @param AuthorizationServer $server
     * @param Session $session
     * @return Response
     * @throws Exception
     */
    public function authorizeUserResponse(Request $request, AuthorizationServer $server, Session $session): Response
    {
        /** @var \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authorizationRequest */
        $authorizationRequest = $session->get('authorizationRequest');
        if ($authorizationRequest === null) {
            throw new Exception("Session seems to have expired");
        }

        /** @var OAuthClient $client */
        $client = $this->getDoctrine()->getRepository(OAuthClient::class)->find($authorizationRequest->getClient()->getIdentifier());

        /** @var \App\Repository\OAuthGrantedScopeRepository $repository */
        $grantedScopeIds = [];
        foreach ($request->request->all() as $k => $v) {
            if (strncmp('scope_', $k, 6) !== 0) {
                continue;
            }
            if ($v !== '1') {
                continue;
            }
            $grantedScopeIds[] = substr($k, 6);
        }
        $repository = $this->getDoctrine()->getRepository(OAuthGrantedScope::class);
        $repository->setGrantedScopes($this->getUser(), $client, $grantedScopeIds);

        $action = $request->request->get('action', 'cancel');
        if ($action !== 'ok') {
            return $this->redirect($authorizationRequest->getRedirectUri());
        }

        $authorizationRequest->setAuthorizationApproved(true);

        $response = $server->completeAuthorizationRequest($authorizationRequest, new \Zend\Diactoros\Response());


        $symfonyResponse = (new HttpFoundationFactory())->createResponse($response);
        return $symfonyResponse;
    }
}