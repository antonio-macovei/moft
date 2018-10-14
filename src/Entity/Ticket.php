<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TicketRepository")
 */
class Ticket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="tickets")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="ticket")
     */
    private $bookings;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Type("\DateTime")
     */
    private $time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxTickets = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WaitingList", mappedBy="ticket", orphanRemoval=true)
     */
    private $waitingLists;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_waiting;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->time = new \DateTime();
        $this->waitingLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setTicket($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getTicket() === $this) {
                $booking->setTicket(null);
            }
        }

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getMaxTickets(): ?int
    {
        return $this->maxTickets;
    }

    public function setMaxTickets(?int $maxTickets): self
    {
        $this->maxTickets = $maxTickets;

        return $this;
    }

    /**
     * @return Collection|WaitingList[]
     */
    public function getWaitingLists(): Collection
    {
        return $this->waitingLists;
    }

    public function addWaitingList(WaitingList $waitingList): self
    {
        if (!$this->waitingLists->contains($waitingList)) {
            $this->waitingLists[] = $waitingList;
            $waitingList->setTicket($this);
        }

        return $this;
    }

    public function removeWaitingList(WaitingList $waitingList): self
    {
        if ($this->waitingLists->contains($waitingList)) {
            $this->waitingLists->removeElement($waitingList);
            // set the owning side to null (unless already changed)
            if ($waitingList->getTicket() === $this) {
                $waitingList->setTicket(null);
            }
        }

        return $this;
    }

    public function getMaxWaiting(): ?int
    {
        return $this->max_waiting;
    }

    public function setMaxWaiting(?int $max_waiting): self
    {
        $this->max_waiting = $max_waiting;

        return $this;
    }
}
