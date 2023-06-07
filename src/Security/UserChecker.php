<?php

namespace App\Security;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        die('entra 1');
        if (!$user instanceof Usuario) {
            return;
        }


        // the message passed to this exception is meant to be displayed to the user
        throw new CustomUserMessageAccountStatusException('El usuario no existe');
    }

    public function checkPostAuth(UserInterface $user): void
    {
        die('entra 2');

        if (!$user instanceof Usuario) {
            return;
        }

        // user account is expired, the user may be notified
        /*if ($user->isExpired()) {
            throw new AccountExpiredException('...');
        }*/
    }
}
