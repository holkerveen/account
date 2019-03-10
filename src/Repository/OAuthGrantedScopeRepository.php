<?php

namespace App\Repository;

use App\Entity\OAuthAccessToken;
use App\Entity\OAuthClient;
use App\Entity\OAuthGrantedScope;
use App\Entity\OAuthScope;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OAuthGrantedScope|null find($id, $lockMode = null, $lockVersion = null)
 * @method OAuthGrantedScope|null findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthGrantedScope[]    findAll()
 * @method OAuthGrantedScope[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthGrantedScopeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OAuthGrantedScope::class);
    }

    /**
     * @param User $user
     * @param OAuthClient $client
     * @param integer[] $scopeIds
     * @throws \Doctrine\ORM\ORMException
     */
    public function setGrantedScopes(
        User $user,
        OAuthClient $client,
        array $scopeIds
    ) {
        $em = $this->getEntityManager();
        /** @var OAuthScopeRepository $scopeRepository */
        $scopeRepository = $em->getRepository(OAuthScope::class);

        $selectedScopes = $scopeRepository->findIn($scopeIds);
        $existingScopes = $this->findGrantedScopes($user,$client);

        $deleteScopes = [];
        $addScopes = [];

        foreach($existingScopes as $existingScope) {
            if(!in_array($existingScope,$selectedScopes)) {
                $deleteScopes[] = $existingScope;
            }
        }

        foreach($selectedScopes as $selectedScope) {
            if(!in_array($selectedScope, $existingScopes)) {
                $addScopes[] = $selectedScope;
            }
        }

        // Delete
        foreach($deleteScopes as $scope) {
            $this->getEntityManager()->remove($this->findOneBy([
                'client'=>$client,
                'user'=>$user,
                'scope'=>$scope,
            ]));
        }

        // Add
        foreach ($addScopes as $scope) {
            $grant = new OAuthGrantedScope();
            $grant->setUser($user);
            $grant->setClient($client);
            $grant->setScope($scope);
            $this->getEntityManager()->persist($grant);
        }

        // Invalidate related access tokens when permissions are changed
        if(count($addScopes) || count($deleteScopes)) {
            /** @var OAuthAccessTokenRepository $tokenRepository */
            $tokenRepository = $this->getEntityManager()->getRepository(OAuthAccessToken::class);
            $tokenRepository->invalidateTokens($client,$user);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param User $user
     * @param OAuthClient $client
     * @return OAuthScope[]
     */
    public function findGrantedScopes(User $user, OAuthClient $client): array
    {
        return array_map(
            function (OAuthGrantedScope $grant) {
                return $grant->getScope();
            },
            $this->findBy(['user' => $user, 'client' => $client])
        );
    }

}

