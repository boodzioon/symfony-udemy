<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class User
{

    private \DateTime $createdAt;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, cascade={"persist", "remove"})
     */
    private $address;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="following")
     */
    private $followed;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="followed")
     */
    private $following;

    /**
     * @ORM\OneToMany(targetEntity=Movie::class, mappedBy="owner")
     */
    private $movie;

    public function __construct()
    {
        $this->followed = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->movie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowed(): Collection
    {
        return $this->followed;
    }

    public function addFollowed(self $followed): self
    {
        if (!$this->followed->contains($followed)) {
            $this->followed[] = $followed;
        }

        return $this;
    }

    public function removeFollowed(self $followed): self
    {
        $this->followed->removeElement($followed);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function addFollowing(self $following): self
    {
        if (!$this->following->contains($following)) {
            $this->following[] = $following;
            $following->addFollowed($this);
        }

        return $this;
    }

    public function removeFollowing(self $following): self
    {
        if ($this->following->removeElement($following)) {
            $following->removeFollowed($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getMovie(): Collection
    {
        return $this->movie;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movie->contains($movie)) {
            $this->movie[] = $movie;
            $movie->setOwner($this);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        if ($this->movie->removeElement($movie)) {
            // set the owning side to null (unless already changed)
            if ($movie->getOwner() === $this) {
                $movie->setOwner(null);
            }
        }

        return $this;
    }
}
