<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\ProductoRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use function Symfony\Component\String\u;
use ApiPlatform\Metadata\ApiProperty;


#[ORM\Entity(repositoryClass: ProductoRepository::class)]
#[ApiResource(
    description: 'Clase producto y sus campos',
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
    ],// normalización del producto
    normalizationContext: [
        'groups' => ['producto:leer']
    ],
    denormalizationContext: [
        'groups' => ['producto:escritura'] // Denormalización del producto
    ],
    paginationItemsPerPage: 10,
)]
#[ApiResource(
    uriTemplate: '/usuarios/{usuario_id}/productos',
    operations: [
        new GetCollection()
    ],
    uriVariables: [
        'usuario_id' => new Link(
            fromProperty:  'productos',
            fromClass: Usuario::class,
        )
    ],
    normalizationContext: [
        'groups' => ['producto:leer']
    ]
)]
#[ApiFilter(PropertyFilter::class)]
#[ApiFilter(SearchFilter::class, properties: ['proveedor.nombreUsuario' => 'partial'])]
class Producto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * ID del producto en la BBDD
     */
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    #[Groups(['producto:leer', 'producto:escritura','usuario:leer','usuario:escritura'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')] // Si coincide de alguna manera lo muestra
    #[Assert\NotBlank]
    #[Assert\Length(min: 2,max: 50,maxMessage: 'Has excedido el máximo número de carácteres', minMessage: 'Mínimo de carácteres requerido es 2')]
    /**
     *  Nombre del producto
     */
    private ?string $nombre = null;

    #[ORM\Column(length: 300)]
    #[Groups(['producto:leer', 'producto:escritura','usuario:escritura'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')] // Si coincide de alguna manera lo muestra
    #[Assert\Length(min: 10,max: 275,maxMessage: 'Has excedido el máximo número de carácteres(275)', minMessage: 'Mínimo de carácteres requerido es 10')]
    #[Assert\NotBlank]
        /**
     * Descripcion del producto
     */
    private ?string $descripcion = null;

    #[ORM\Column(length: 300)]
    #[Groups(['producto:leer', 'producto:escritura','usuario:escritura'])]
    #[Assert\NotBlank(message: 'No puedes dejarlo en blanco')]
     /**
     * Código de referencia del producto
     */
    private ?string $codRef = null;

    #[ORM\Column(length: 500)]
    #[Groups(['producto:leer', 'producto:escritura','usuario:escritura'])]
    #[SerializedName('urlImagen')] // Nombre que se le da al atributo al serializarse ( Solo para la escritura )
    #[Assert\NotBlank]
    /**
     * Imagen del producto
     */
    private ?string $imgProducto = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('producto:leer')]
    /**
     * Fecha de creación del producto
     */
    private ?\DateTimeInterface $fechaCreacion = null; 

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Proveedor del producto
     */
    #[ORM\ManyToOne(inversedBy: 'productos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['producto:leer', 'producto:escritura'])]
    #[Assert\NotBlank]
    #[Assert\Valid]
    #[ApiFilter(SearchFilter::class,strategy: 'exact')]
    private ?Usuario $proveedor = null;
    
    #[ORM\Column(length: 300, type: 'float')]
    #[Groups(['producto:leer', 'producto:escritura','usuario:escritura'])]
    #[Assert\NotBlank]
    private ?float $precio = null;

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    #[Groups(['producto:leer', 'usuario:leer','usuario:escritura'])]
    public function getDescripcionCorta(): ?string
    {
        return u($this->descripcion) ->truncate(40,'...') ; // Muestra una descripcion de 40 caracteres y luego añade el final ...
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getCodRef(): ?string
    {
        return $this->codRef;
    }

    public function setCodRef(string $codRef): self
    {
        $this->codRef = $codRef;

        return $this;
    }

    public function getImgProducto(): ?string
    {
        return $this->imgProducto;
    }

    public function setImgProducto(string $imgProducto): self
    {
        $this->imgProducto = $imgProducto;

        return $this;
    }

    /**
     * Formato más accesible para el entendimiento de cualquier persona
     */
    public function getFechaCreacion(): \DateTime
    {
        return $this->fechaCreacion ;        
        //return Carbon::instance($this->fechaCreacion)->diffForHumans();
    }

    public function setFechaCreacion(\DateTimeImmutable $fechaCreacion): self
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }
    public function __construct()
    {
        $this->fechaCreacion = new \DateTimeImmutable();
    }

    public function getProveedor(): ?Usuario
    {
        return $this->proveedor;
    }

    public function setProveedor(?Usuario $proveedor): self
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function getFormatPrice(){
        $formatPrice = number_format($this->precio,2,',','.').'€';
        return $formatPrice;
    }

    public function setPrecio(float $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

}
