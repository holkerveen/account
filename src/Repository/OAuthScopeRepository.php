<?php

namespace App\Repository;

use App\Entity\OAuthClient;
use App\Entity\OAuthGrantedScope;
use App\Entity\OAuthScope;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OAuthScope|null find($id, $lockMode = null, $lockVersion = null)
 * @method OAuthScope|null findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthScope[]    findAll()
 * @method OAuthScope[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthScopeRepository extends ServiceEntityRepository implements ScopeRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OAuthScope::class);
    }

    /**
     * @param array $ids
     * @return OAuthScope[]
     */
    public function findIn(array $ids): array {
        $qb = $this->createQueryBuilder('s')->where('s.id IN (:ids)');
        return $qb->getQuery()->execute([ 'ids' => $ids ] );
    }

    // /**
    //  * @return OAuthScope[] Returns an array of OAuthScope objects
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
    public function findOneBySomeField($value): ?OAuthScope
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
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return ScopeEntityInterface
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        return $this->findOneBy([
            'code' => $identifier,
        ]);
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @param null|string $userIdentifier
     *
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->find($userIdentifier);

        /** @var OAuthClient $client */
        $client = $this->getEntityManager()->getRepository(OAuthClient::class)->find($clientEntity->getIdentifier());

        /** @var OAuthGrantedScopeRepository $grantedScopeRepository */
        $grantedScopeRepository = $this->getEntityManager()->getRepository(OAuthGrantedScope::class);

        $grantedScopes = $grantedScopeRepository->findGrantedScopes($user,$client);

        $intersect = [];
        foreach($scopes as $scope) {
            if(in_array($scope,$grantedScopes)) $intersect[] = $scope;
        }

        return $intersect;
    }
}
