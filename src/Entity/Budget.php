<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BudgetRepository")
 */
class Budget
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $budget_amount;

    /**
     * @ORM\Column(type="date")
     */
    private $budget_date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="budgets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="budget")
     */
    private $account;

    /**
     * @ORM\Column(type="integer")
     */
    private $active;

    /**
     * @ORM\Column(type="integer")
     */
    private $original_budget_amount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBudgetAmount(): ?int
    {
        return $this->budget_amount;
    }

    public function setBudgetAmount(int $budget_amount): self
    {
        $this->budget_amount = $budget_amount;

        return $this;
    }

    public function getBudgetDate(): ?\DateTimeInterface
    {
        return $this->budget_date;
    }

    public function setBudgetDate(\DateTimeInterface $budget_date): self
    {
        $this->budget_date = $budget_date;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

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

    public function getOriginalBudgetAmount(): ?int
    {
        return $this->original_budget_amount;
    }

    public function setOriginalBudgetAmount(int $original_budget_amount): self
    {
        $this->original_budget_amount = $original_budget_amount;

        return $this;
    }
}
