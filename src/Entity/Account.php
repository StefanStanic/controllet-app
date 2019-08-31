<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="account")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Budget", mappedBy="account")
     */
    private $budgets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bills", mappedBy="account")
     */
    private $bills;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", inversedBy="accounts")
     */
    private $currency;


    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->budget = new ArrayCollection();
        $this->budgets = new ArrayCollection();
        $this->bills = new ArrayCollection();
    }

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

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getAccount() === $this) {
                $transaction->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Budget[]
     */
    public function getBudgets(): Collection
    {
        return $this->budgets;
    }

    public function addBudget(Budget $budget): self
    {
        if (!$this->budgets->contains($budget)) {
            $this->budgets[] = $budget;
            $budget->setAccount($this);
        }

        return $this;
    }

    public function removeBudget(Budget $budget): self
    {
        if ($this->budgets->contains($budget)) {
            $this->budgets->removeElement($budget);
            // set the owning side to null (unless already changed)
            if ($budget->getAccount() === $this) {
                $budget->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Bills[]
     */
    public function getBills(): Collection
    {
        return $this->bills;
    }

    public function addBill(Bills $bill): self
    {
        if (!$this->bills->contains($bill)) {
            $this->bills[] = $bill;
            $bill->setAccount($this);
        }

        return $this;
    }

    public function removeBill(Bills $bill): self
    {
        if ($this->bills->contains($bill)) {
            $this->bills->removeElement($bill);
            // set the owning side to null (unless already changed)
            if ($bill->getAccount() === $this) {
                $bill->setAccount(null);
            }
        }

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }
}
