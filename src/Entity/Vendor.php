<?php

namespace App\Entity;

use App\Repository\VendorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "/api/vendor",
 *          parameters = { "id" = "expr(object.getId())" }
 *      )
 * )
 *
 */

#[ORM\Entity(repositoryClass: VendorRepository::class)]
class Vendor implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(["getUsersByVendor", "getUser"])]
  private ?int $id = null;

  #[ORM\Column(length: 180, unique: true)]
  #[Groups(["getUsersByVendor", "getUser"])]
  private ?string $email = null;

  #[ORM\Column]
  private array $roles = [];

  /**
   * @var string The hashed password
   */
  #[ORM\Column]
  private ?string $password = null;

  #[ORM\Column(length: 255)]
  #[Groups(["getUsersByVendor", "getUser"])]
  private ?string $name = null;

  #[ORM\Column]
  #[Groups(["getUsersByVendor", "getUser"])]
  private ?\DateTimeImmutable $updatedAt = null;

  #[ORM\Column]
  #[Groups(["getUsersByVendor", "getUser"])]
  private ?\DateTimeImmutable $createdAt = null;

  #[ORM\OneToMany(mappedBy: 'vendor', targetEntity: Customer::class, orphanRemoval: true)]
  #[Groups(["getUsersByVendor"])]
  private Collection $customers;

  public function __construct()
  {
    $this->customers = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): self
  {
    $this->email = $email;

    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  /**
   * M??thode getUsername qui permet de retourner le champ qui est utilis?? pour l'authentification.
   *
   * @return string
   */
  public function getUsername(): string
  {
    return $this->getUserIdentifier();
  }

  /**
   * @see UserInterface
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  public function setRoles(array $roles): self
  {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): self
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials()
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
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

  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): self
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  /**
   * @return Collection<int, Customer>
   */
  public function getCustomers(): Collection
  {
    return $this->customers;
  }

  public function addCustomers(Customer $customer): self
  {
    if (!$this->customers->contains($customer)) {
      $this->customers->add($customer);
      $customer->setVendor($this);
    }

    return $this;
  }

  public function removeCustomer(Customer $customer): self
  {
    if ($this->customers->removeElement($customer)) {
      // set the owning side to null (unless already changed)
      if ($customer->getVendor() === $this) {
        $customer->setVendor(null);
      }
    }

    return $this;
  }
}
