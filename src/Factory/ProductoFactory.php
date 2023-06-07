<?php

namespace App\Factory;

use App\Entity\Producto;
use App\Repository\ProductoRepository;
use phpDocumentor\Reflection\Types\Self_;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Producto>
 *
 * @method        Producto|Proxy create(array|callable $attributes = [])
 * @method static Producto|Proxy createOne(array $attributes = [])
 * @method static Producto|Proxy find(object|array|mixed $criteria)
 * @method static Producto|Proxy findOrCreate(array $attributes)
 * @method static Producto|Proxy first(string $sortedField = 'id')
 * @method static Producto|Proxy last(string $sortedField = 'id')
 * @method static Producto|Proxy random(array $attributes = [])
 * @method static Producto|Proxy randomOrCreate(array $attributes = [])
 * @method static ProductoRepository|RepositoryProxy repository()
 * @method static Producto[]|Proxy[] all()
 * @method static Producto[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Producto[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Producto[]|Proxy[] findBy(array $attributes)
 * @method static Producto[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Producto[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class ProductoFactory extends ModelFactory
{
    private const NOMBRE_PRODUCTOS = ['par de zapatillas para correr', 'set de mancuernas', 'colchoneta de yoga', 'rodillo de espuma', 'guantes de boxeo', 'set de bandas de resistencia', 'cuerda de saltar', 'rastreador de fitness', 'mochila de hidratación', 'gafas de natación', 'set de palos de golf', 'raqueta de tenis', 'balón de baloncesto', 'balón de voleibol', 'balón de fútbol', 'palo de hockey', 'tabla de skate', 'tabla de snowboard', 'bicicleta de montaña', 'arnés de escalada', 'par de botas de esquí', 'par de botas de senderismo', 'kayak', 'tabla de surf', 'par de patines de ruedas', 'set de paletas de ping pong', 'bate de béisbol'];
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'codRef' => self::faker()->randomNumber(),
            'descripcion' => self::faker()->paragraph(),
            'fechaCreacion' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year')),
            'imgProducto' => 'https://picsum.photos/100/100',
            'nombre' => self::faker()->randomElement(self::NOMBRE_PRODUCTOS),
            'proveedor' => UsuarioFactory::new(),
            'precio' => self::faker()->randomFloat()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Producto $producto): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Producto::class;
    }
}
