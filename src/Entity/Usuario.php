<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'producto:leer']
        ),
        new GetCollection(),
    ],
    formats: [
        'jsonld',
        'json',
        'html',
        'jsonhal',
        'csv' => 'text/csv'
    ],
    normalizationContext: ['groups' => ['usuario:leer']],
    denormalizationContext: ['groups' => ['usuario:escritura']]
)]
#[ApiResource(
    uriTemplate: '/productos/{producto_id}/proveedor.{_format}',
    operations: [
        new Get()
    ],
    uriVariables: [
        'producto_id' => new Link(
            fromProperty: 'proveedor'
            ,fromClass: Producto::class
        )
    ],
    normalizationContext: ['groups' => ['usuario:leer']],
)]
#[UniqueEntity(fields: ['email'],message: 'Este email ya exite.')]
#[UniqueEntity(fields: ['nombreUsuario'],message: 'Este usuario ya exite.')]
#[ApiFilter(PropertyFilter::class)]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['usuario:leer','usuario:escritura'])]
    #[Assert\Email(message: 'Email no valido')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = ['ROLES_USER'];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['usuario:escritura'])]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(['usuario:leer','usuario:escritura','producto:leer', 'producto:escritura'])]
    #[Assert\NotBlank]
    private ?string $nombreUsuario = null;

    #[ORM\OneToMany(mappedBy: 'proveedor', targetEntity: Producto::class, orphanRemoval: true,cascade: ['persist'])]
    #[Groups(['usuario:leer', 'usuario:escritura'])]
    #[Assert\Valid]
    private Collection $productos;


    #[Groups(['usuario:leer','usuario:escritura'])]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $Telefono = null;

    #[Groups(['usuario:leer','usuario:escritura'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Localidad = null;

    public function __construct()
    {
        $this->productos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
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
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
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

    public function getNombreUsuario(): ?string
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario(string $nombreUsuario): self
    {
        $this->nombreUsuario = $nombreUsuario;

        return $this;
    }

    /**
     * @return Collection<int, Producto>
     */
    public function getProductos(): Collection
    {
        return $this->productos;
    }

    public function addProducto(Producto $producto): self
    {
        if (!$this->productos->contains($producto)) {
            $this->productos->add($producto);
            $producto->setProveedor($this);
        }

        return $this;
    }

    public function removeProducto(Producto $producto): self
    {
        if ($this->productos->removeElement($producto)) {
            // set the owning side to null (unless already changed)
            if ($producto->getProveedor() === $this) {
                $producto->setProveedor(null);
            }
        }

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->Telefono;
    }

    public function setTelefono(?string $Telefono): self
    {
        $this->Telefono = $Telefono;

        return $this;
    }

    public function getLocalidad(): ?string
    {
        return $this->Localidad;
    }

    public function setLocalidad(?string $Localidad): self
    {
        $this->Localidad = $Localidad;

        return $this;
    }
}
