<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface AuthoredEntityinterface
{
    public function setAuthor(UserInterface $user): AuthoredEntityinterface;
}