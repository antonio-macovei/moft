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

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $university;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $faculty;

    /**
     * @ORM\Column(type="integer")
     */
    private $studentId;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $facebook;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WaitingList", mappedBy="user", orphanRemoval=true)
     */
    private $waitingLists;

    public function __construct()
    {
        parent::__construct();
        $this->blacklists = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->addRole("ROLE_USER");
        $this->waitingLists = new ArrayCollection();
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getUniversity(): ?string
    {
        return $this->university;
    }

    public function setUniversity(string $university): self
    {
        $this->university = $university;

        return $this;
    }

    public function getFaculty(): ?string
    {
        return $this->faculty;
    }

    public function setFaculty(string $faculty): self
    {
        $this->faculty = $faculty;

        return $this;
    }

    public function getStudentId(): ?int
    {
        return $this->studentId;
    }

    public function setStudentId(int $studentId): self
    {
        $this->studentId = $studentId;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Overridden so that username is now optional
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->setUsername($email);
        return parent::setEmail($email);
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
            $waitingList->setUser($this);
        }

        return $this;
    }

    public function removeWaitingList(WaitingList $waitingList): self
    {
        if ($this->waitingLists->contains($waitingList)) {
            $this->waitingLists->removeElement($waitingList);
            // set the owning side to null (unless already changed)
            if ($waitingList->getUser() === $this) {
                $waitingList->setUser(null);
            }
        }

        return $this;
    }
}