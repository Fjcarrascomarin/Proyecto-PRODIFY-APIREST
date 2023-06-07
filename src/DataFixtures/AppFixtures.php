<?php

namespace App\DataFixtures;

use App\Factory\ProductoFactory;
use App\Factory\UsuarioFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Creamos los usuarios de prueba
        UsuarioFactory::createMany(10);
        // Creamos 40 objetos del objeto Producto aleatorios para probar el funcionamiento
        ProductoFactory::createMany(40, function() {
            return [
                'proveedor' => UsuarioFactory::random(),
            ];
        });

    }
}
