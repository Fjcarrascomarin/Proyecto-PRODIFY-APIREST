<?php

namespace App\Factory;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Usuario>
 *
 * @method        Usuario|Proxy create(array|callable $attributes = [])
 * @method static Usuario|Proxy createOne(array $attributes = [])
 * @method static Usuario|Proxy find(object|array|mixed $criteria)
 * @method static Usuario|Proxy findOrCreate(array $attributes)
 * @method static Usuario|Proxy first(string $sortedField = 'id')
 * @method static Usuario|Proxy last(string $sortedField = 'id')
 * @method static Usuario|Proxy random(array $attributes = [])
 * @method static Usuario|Proxy randomOrCreate(array $attributes = [])
 * @method static UsuarioRepository|RepositoryProxy repository()
 * @method static Usuario[]|Proxy[] all()
 * @method static Usuario[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Usuario[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Usuario[]|Proxy[] findBy(array $attributes)
 * @method static Usuario[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Usuario[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class UsuarioFactory extends ModelFactory
{
    private $passwordEncoder;
    private const NOMBRE_USUARIOS = [
        ["Alice", ["ROLE_ADMIN"]],
        ["Bob", ["ROLE_ADMIN"]],
        ["Charlie", ["ROLE_ADMIN"]],
        ["David", ["ROLE_ADMIN"]],
        ["Eve", ["ROLE_ADMIN"]],
        ["Frank", ["ROLE_ADMIN"]],
        ["Grace", ["ROLE_ADMIN"]],
    ];
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        $re = self::faker()->randomElement(self::NOMBRE_USUARIOS);

        return [
            'email' => self::faker()->email(),
            'nombreUsuario' => $re[0].self::faker()->randomNumber(3),
            'password' => $this->passwordEncoder->hashPassword(new Usuario(), '123'),
            'roles' => $re[1],
            'telefono'=>self::faker()->randomNumber(9),
            'localidad'=>self::faker()->country()
        ];
    }

    protected static function getClass(): string
    {
        return Usuario::class;
    }
}
