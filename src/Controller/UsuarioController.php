<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\CambiarContrasenaUsuarioType;
use App\Form\EditarUsuarioType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/usuarios')]
class UsuarioController extends AbstractController
{
    private $entityManager, $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'usuarios_listado',)]
    public function listado(): RedirectResponse|Response
    {
        try{
            $usuario = $this->getUser();
            $nombreUsuario = $usuario->getNombreUsuario();
            $rolUsuario = $usuario->getRoles()[0];
            $usuariosFinal = [];


            $todosUsuarios = $this->entityManager->getRepository(Usuario::class)->createQueryBuilder('p')
                ->getQuery()->getResult();

            foreach ($todosUsuarios as $usuarioLista) {
                $usuariosFinal[] = [
                    'id' => $usuarioLista->getId(),
                    'nombre' => $usuarioLista->getNombreUsuario(),
                    'email' => $usuarioLista->getEmail(),
                    'roles' => $usuarioLista->getRoles()[0],
                    'telefono' => $usuarioLista->getTelefono(),
                    'localidad' => $usuarioLista->getLocalidad()
                ];
            }
        }catch (AccessDeniedException $accessDeniedException){
            return $this->redirectToRoute('productos_listado');
        }

        return $this->render('usuarios/listadoUsuarios.html.twig', [
            'usuarios' => $usuariosFinal,
            'nombreUsuario' => $nombreUsuario,
            'rolUsuario' =>$rolUsuario
        ]);
    }


    #[Route('/editar-perfil', name: 'editar_perfil')]
     public function editarUsuario(Request $request, Usuario $usuario = null): RedirectResponse|Response
    {
        $usuario = $this->getUser();

        $form = $this->createForm(EditarUsuarioType::class, $usuario);

        $form->handleRequest($request);
        $errors = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($usuario);
            $this->entityManager->flush();

            return $this->redirectToRoute('productos_listado');
        } else {
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
        }

        return $this->render('registro/registro.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/borrar/{id}','borrar_usuario')]
    public function deleteGuestAction(Usuario $usuario): RedirectResponse
    {
        if (!$usuario) {
            throw $this->createNotFoundException('No guest found');
        }

        $this->entityManager->remove($usuario);
        $this->entityManager->flush();

        return $this->redirectToRoute('usuarios_listado');
    }

    #[Route('/cambiar-contrasena', name: 'cambiar_contrasena')]
    public function cambiarContrasena(Request $request): RedirectResponse|Response
    {
        /** @var Usuario $usuario */
        $usuario = $this->getUser();

        $form = $this->createForm(CambiarContrasenaUsuarioType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuario->setPassword($form->get('password')->getData());

            $hashedPassword = $this->passwordHasher->hashPassword($usuario, $form->get('password')->getData());

            $usuario->setPassword($hashedPassword);

            $this->entityManager->persist($usuario);
            $this->entityManager->flush();

            return $this->redirectToRoute('productos_listado');
        }

        return $this->render('usuarios/cambiar-contrasena.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView()
        ]);
    }
}
