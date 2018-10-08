<?php
// src/Entity/User.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Blacklist", mappedBy="user", orphanRemoval=true)
     */
    private $blacklists;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="user", orphanRemoval=true)
     */
    private $bookings;

    public function __construct()
    {
        parent::__construct();
        $this->blacklists = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        // your own logic
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection|Blacklist[]
     */
    public function getBlacklists(): Collection
    {
        return $this->blacklists;
    }

    public function addBlacklist(Blacklist $blacklist): self
    {
        if (!$this->blacklists->contains($blacklist)) {
            $this->blacklists[] = $blacklist;
            $blacklist->setUser($this);
        }

        return $this;
    }

    public function removeBlacklist(Blacklist $blacklist): self
    {
        if ($this->blacklists->contains($blacklist)) {
            $this->blacklists->removeElement($blacklist);
            // set the owning side to null (unless already changed)
            if ($blacklist->getUser() === $this) {
                $blacklist->setUser(null);
            }
        }

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
            $booking->setUser($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getUser() === $this) {
                $booking->setUser(null);
            }
        }

        return $this;
    }
}