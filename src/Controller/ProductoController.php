<?php


namespace App\Controller;

use App\Entity\Producto;
use App\Form\ProductoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/productos')]
class ProductoController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/listado', name: 'productos_listado')]
    public function listado(): Response
    {
        $usuario = $this->getUser();
        $nombreUsuario = $usuario->getNombreUsuario();
        $rolUsuario = $usuario->getRoles()[0];
        $productosFinal = [];

        if ($rolUsuario === 'ROLES_USER' || $rolUsuario === 'ROLE_USER') {

            $productosUsuario = $this->em->getRepository(Producto::class)->createQueryBuilder('p')
                ->join('p.proveedor', 'prv')
                ->where('prv = :proveedor')
                ->setParameters([
                    'proveedor' => $usuario
                ])
                ->getQuery()->getResult();

            foreach ($productosUsuario as $producto) {
                $format = $producto->getPrecio();

                $productosFinal[] = [
                    'id' => $producto->getId(),
                    'nombre' => $producto->getNombre(),
                    'descripcion' => $producto->getDescripcion(),
                    'cod_ref' => $producto->getCodRef(),
                    'img_producto' => $producto->getImgProducto(),
                    'precio' => $producto->getFormatPrice(),
                    'proveedor' => $producto->getProveedor()->getNombreUsuario()
                ];
            }

        } elseif ($rolUsuario === 'ROLE_ADMIN' || $rolUsuario === 'ROLES_ADMIN') {
            $productosTodosUsuarios = $this->em->getRepository(Producto::class)->createQueryBuilder('p')
                ->getQuery()->getResult();

            foreach ($productosTodosUsuarios as $producto) {
                $productosFinal[] = [
                    'id' => $producto->getId(),
                    'nombre' => $producto->getNombre(),
                    'descripcion' => $producto->getDescripcion(),
                    'cod_ref' => $producto->getCodRef(),
                    'img_producto' => $producto->getImgProducto(),
                    'precio' => $producto->getFormatPrice(),
                    'proveedor' => $producto->getProveedor()->getNombreUsuario()
                ];
            }
        }

        return $this->render('productos/listado.html.twig', [
            'productos' => $productosFinal,
            'nombreUsuario' => $nombreUsuario,
            'rolUsuario' =>$rolUsuario
        ]);
    }


    #[Route('/nuevo',name: 'crear_producto')]
    #[Route('/editar/{producto}',name: 'editar_producto')]
    public function formulario(Request $request, Producto $producto = null)
    {
        $msgOk = '';
        $msgError = '';

        if(!$producto){
            $producto = new Producto();
            $producto->setProveedor($this->getUser());
            $this->em->persist($producto);
        }

        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->em->flush();

                $this->addFlash('success', $msgOk);

                return $this->redirectToRoute('productos_listado');
            } catch (\Exception $e) {
                $this->addFlash('error', $msgError);
            }
        }

        return $this->render('productos/formulario.html.twig', [
            'producto' => $producto,
            'form' => $form->createView()
        ]);
    }

    #[Route('/borrar/{id}','borrar')]
    public function deleteGuestAction(Producto $producto)
    {
        if (!$producto) {
            throw $this->createNotFoundException('No guest found');
        }

        $this->em->remove($producto);
        $this->em->flush();

        return $this->redirectToRoute('productos_listado');
    }


}