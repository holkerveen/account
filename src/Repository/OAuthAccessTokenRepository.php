<?php

namespace App\Repository;

use App\Entity\OAuthAccessToken;
use App\Entity\OAuthClient;
use App\Entity\OAuthScope;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OAuthAccessToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method OAuthAccessToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthAccessToken[]    findAll()
 * @method OAuthAccessToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthAccessTokenRepository extends ServiceEntityRepository implements AccessTokenRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OAuthAccessToken::class);
    }

    // /**
    //  * @return OAuthAccessToken[] Returns an array of OAuthAccessToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OAuthAccessToken
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * Create a new access token
     *
     * @param ClientEntityInterface $clientEntity
     * @param ScopeEntityInterface[] $scopes
     * @param mixed $userIdentifier
     *
     * @return AccessTokenEntityInterface
     * @throws ORMException
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $token = new OAuthAccessToken();
        /** @var OAuthClient $client */
        $token->setClient($clientEntity);
        /** @var UserRepository $userRepository */
        $userRepository = $this->getEntityManager()->getRepository(User::class);
        $token->setUser($userRepository->find($userIdentifier));

        return $token;
    }

    /**
     * Persists a new access token to permanent storage.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     * @throws ORMException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        /** @var OAuthAccessToken $accessTokenEntity */
        $client = $accessTokenEntity->getClient();
        $accessTokenEntity->setClient($this->getEntityManager()->merge($client));

        /** @var OAuthScope $scope */
        foreach($accessTokenEntity->getScopes() as $scope) {
            $accessTokenEntity->removeScope($scope);
            $accessTokenEntity->addScope($this->getEntityManager()->merge($scope));
        }

        $this->getEntityManager()->persist($accessTokenEntity);
        $this->getEntityManager()->flush();
    }

    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     * @throws ORMException
     */
    public function revokeAccessToken($tokenId)
    {
        $this->getEntityManager()->remove($this->find($tokenId));
        $this->getEntityManager()->flush();
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isAccessTokenRevoked($tokenId)
    {
        return !($this->find($tokenId) instanceof OAuthAccessToken);
    }

    /**
     * @param \App\Entity\OAuthClient $client
     * @param \App\Entity\User $user
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function invalidateTokens(\App\Entity\OAuthClient $client, \App\Entity\User $user)
    {
        $qb = $this->createQueryBuilder('t')
            ->delete()
            ->where('t.client = :clientId')
            ->andWhere('t.user = :userId');
        $qb->getQuery()->execute([
            'clientId' => $client->getId(),
            'userId' => $user->getId(),
        ]);
    }
}
