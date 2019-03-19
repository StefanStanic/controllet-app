<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $account_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $account_type;

    /**
     * @ORM\Column(type="integer")
     */
    private $account_balance;

    /**
     * @ORM\Column(type="datetime")
     */
    private $last_used_date;

    /**
     * @ORM\Column(type="integer")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="account")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountName(): ?string
    {
        return $this->account_name;
    }

    public function setAccountName(string $account_name): self
    {
        $this->account_name = $account_name;

        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->account_type;
    }

    public function setAccountType(string $account_type): self
    {
        $this->account_type = $account_type;

        return $this;
    }

    public function getAccountBalance(): ?int
    {
        return $this->account_balance;
    }

    public function setAccountBalance(int $account_balance): self
    {
        $this->account_balance = $account_balance;

        return $this;
    }

    public function getLastUsedDate(): ?\DateTimeInterface
    {
        return $this->last_used_date;
    }

    public function setLastUsedDate(\DateTimeInterface $last_used_date): self
    {
        $this->last_used_date = $last_used_date;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
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
}
