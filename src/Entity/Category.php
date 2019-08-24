<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
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
    private $category_name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="category")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SubCategory", mappedBy="category")
     */
    private $subCategory;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Budget", mappedBy="category")
     */
    private $budgets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bills", mappedBy="category")
     */
    private $bills;


    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->subCategory = new ArrayCollection();
        $this->budgets = new ArrayCollection();
        $this->bills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->category_name;
    }

    public function setCategoryName(string $category_name): self
    {
        $this->category_name = $category_name;

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
            $transaction->setCategory($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getCategory() === $this) {
                $transaction->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SubCategory[]
     */
    public function getSubCategory(): Collection
    {
        return $this->subCategory;
    }

    public function addSubCategory(SubCategory $subCategory): self
    {
        if (!$this->subCategory->contains($subCategory)) {
            $this->subCategory[] = $subCategory;
            $subCategory->setCategory($this);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory): self
    {
        if ($this->subCategory->contains($subCategory)) {
            $this->subCategory->removeElement($subCategory);
            // set the owning side to null (unless already changed)
            if ($subCategory->getCategory() === $this) {
                $subCategory->setCategory(null);
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
            $budget->setCategory($this);
        }

        return $this;
    }

    public function removeBudget(Budget $budget): self
    {
        if ($this->budgets->contains($budget)) {
            $this->budgets->removeElement($budget);
            // set the owning side to null (unless already changed)
            if ($budget->getCategory() === $this) {
                $budget->setCategory(null);
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
            $bill->setCategory($this);
        }

        return $this;
    }

    public function removeBill(Bills $bill): self
    {
        if ($this->bills->contains($bill)) {
            $this->bills->removeElement($bill);
            // set the owning side to null (unless already changed)
            if ($bill->getCategory() === $this) {
                $bill->setCategory(null);
            }
        }

        return $this;
    }
}
