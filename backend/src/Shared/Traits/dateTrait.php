<?php

namespace App\Shared\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait dateTrait
{
    #[ORM\Column(name: 'created_at', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\PrePersist]
    public function initDates(): void
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    #[ORM\PreUpdate]
    public function touchUpdatedAt(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $date): static
    {
        $this->createdAt = $date;
        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $date): static
    {
        $this->updatedAt = $date;
        return $this;
    }
}

