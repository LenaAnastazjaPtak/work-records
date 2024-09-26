<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    /**
     * @var Collection<int, WorkingTime>
     */
    #[ORM\OneToMany(targetEntity: WorkingTime::class, mappedBy: 'employee', orphanRemoval: true)]
    private Collection $workingTimes;

    public function __construct()
    {
        $this->workingTimes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, WorkingTime>
     */
    public function getWorkingTimes(): Collection
    {
        return $this->workingTimes;
    }

    public function addWorkingTime(WorkingTime $workingTime): static
    {
        if (!$this->workingTimes->contains($workingTime)) {
            $this->workingTimes->add($workingTime);
            $workingTime->setEmployee($this);
        }

        return $this;
    }

    public function removeWorkingTime(WorkingTime $workingTime): static
    {
        if ($this->workingTimes->removeElement($workingTime)) {
            // set the owning side to null (unless already changed)
            if ($workingTime->getEmployee() === $this) {
                $workingTime->setEmployee(null);
            }
        }

        return $this;
    }
}
