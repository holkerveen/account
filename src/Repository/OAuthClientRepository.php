<?php

namespace App\Repository;

use App\Entity\OAuthClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OAuthClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method OAuthClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthClient[]    findAll()
 * @method OAuthClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OAuthClient::class);
    }

    // /**
    //  * @return OAuthClient[] Returns an array of OAuthClient objects
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
    public function findOneBySomeField($value): ?OAuthClient
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
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     * @param null|string $grantType The grant type used (if sent)
     * @param null|string $clientSecret The client's secret (if sent)
     * @param bool $mustValidateSecret If true the client must attempt to validate the secret if the client
     *                                        is confidential
     *
     * @return OAuthClient
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getClientEntity(
        $clientIdentifier,
        $grantType = null,
        $clientSecret = null,
        $mustValidateSecret = true
    ):OAuthClient {
        /** @var OAuthClient $client */
        $client = $this->createQueryBuilder('client')
            ->andWhere('client.id = :id')
            ->setParameter('id', $clientIdentifier)
            ->getQuery()
            ->getOneOrNullResult()
            ;
        if(!$client || !password_verify($clientSecret,$client->getSecret())) {
            usleep(rand(100000,1999999));
            OAuthServerException::invalidClient();
        }
        return $client;
    }
}
