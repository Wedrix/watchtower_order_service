<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name:"`user`")]
class User
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private int $id;
    
    /**
     * @var Collection<int,Order>
     */
    #[OneToMany(targetEntity:Order::class,mappedBy:'user',cascade:['persist','remove'])]
    private Collection $orders;

    /**
     * @var Collection<int,Session>
     */
    #[OneToMany(targetEntity:Session::class,mappedBy:'user',cascade:['persist','remove'])]
    private Collection $sessions;

    public function __construct(
        #[Column]
        private string $name,
        #[Column(unique:true)]
        private string $email,
        #[Column]
        private string $password,
        #[Column]
        private string $role
    )
    {
        $this->orders = new ArrayCollection();
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(
        string $name
    ): void
    {
        $this->name = $name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function setEmail(
        string $email
    ): void
    {
        $this->email = $email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function setPassword(
        string $password
    ): void
    {
        $this->password = $password;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function setRole(
        string $role
    ): void
    {
        $this->role = $role;
    }

    /**
     * @return Collection<int,Order>
     */
    public function orders(): Collection
    {
        return $this->orders;
    }

    /**
     * @return Collection<int,Session>
     */
    public function sessions(): Collection
    {
        return $this->sessions;
    }
}