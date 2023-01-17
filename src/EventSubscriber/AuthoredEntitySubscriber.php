<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\AuthoredEntityinterface;
use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthoredEntitySubscriber implements EventSubscriberInterface
{
    private $tokenStorage;


    public function __construct(
        TokenStorageInterface $tokenStorage,

    ) {
        $this->tokenStorage = $tokenStorage;

    }


    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['getAuthenticatedUser', EventPriorities::PRE_VALIDATE]
        ];
    }

    public function getAuthenticatedUser(ViewEvent $event){
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        $token = $this->tokenStorage->getToken();

        /** @var UserInterface $user */
        $user = $token->getUser();

        if(!$entity instanceof AuthoredEntityinterface || Request::METHOD_POST !== $method || !$user instanceof User){
            return;
        }
        $entity->setAuthor($user);


    }
}