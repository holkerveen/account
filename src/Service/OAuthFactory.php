<?php

namespace App\Service;

use App\Repository\OAuthAccessTokenRepository;
use App\Repository\OAuthClientRepository;
use App\Repository\OAuthScopeRepository;
use DateInterval;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\ImplicitGrant;
use Symfony\Bridge\Doctrine\RegistryInterface;

class OAuthFactory
{

    /**
     * @param RegistryInterface $registry
     * @return AuthorizationServer
     * @throws \Exception
     */
    public static function createAuthorizationServer(RegistryInterface $registry): AuthorizationServer
    {
        $server = new AuthorizationServer(
            new OAuthClientRepository($registry),
            new OAuthAccessTokenRepository($registry),
            new OAuthScopeRepository($registry),
            new CryptKey(getenv('OAUTH_PRIVATE_KEY_FILE'), null, false),
            getenv('OAUTH_ENCRYPTION_KEY')
        );

        $server->enableGrantType(
            new ImplicitGrant(new DateInterval('PT1H')),
            new DateInterval(('PT1H'))
        );

        return $server;
    }
}