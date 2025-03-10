<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubCategoryRepository")
 */
class SubCategory
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
    private $subCategoryName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="subCategory")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="subCategory")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bills", mappedBy="subcategory")
     */
    private $bills;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->bills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubCategoryName(): ?string
    {
        return $this->subCategoryName;
    }

    public function setSubCategoryName(string $subCategoryName): self
    {
        $this->subCategoryName = $subCategoryName;

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
            $transaction->setSubCategory($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getSubCategory() === $this) {
                $transaction->setSubCategory(null);
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
            $bill->setSubcategory($this);
        }

        return $this;
    }

    public function removeBill(Bills $bill): self
    {
        if ($this->bills->contains($bill)) {
            $this->bills->removeElement($bill);
            // set the owning side to null (unless already changed)
            if ($bill->getSubcategory() === $this) {
                $bill->setSubcategory(null);
            }
        }

        return $this;
    }
}
