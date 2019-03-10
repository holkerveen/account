<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="oauth_granted_scope",indexes={@ORM\Index(name="user_idx", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\OAuthGrantedScopeRepository")
 * @UniqueEntity(fields={"user","client","scope"}, message="The user already has given permissin for that scope to that client")
 */
class OAuthGrantedScope
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @var User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OAuthClient")
     * @ORM\JoinColumn(nullable=false)
     * @var OAuthClient
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OAuthScope")
     * @ORM\JoinColumn(nullable=false)
     * @var OAuthScope
     */
    private $scope;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getClient(): ?OAuthClient
    {
        return $this->client;
    }

    public function setClient(ClientEntityInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getScope(): ?OAuthScope
    {
        return $this->scope;
    }

    public function setScope(?OAuthScope $scope)
    {
        $this->scope = $scope;

        return $this;
    }

}
