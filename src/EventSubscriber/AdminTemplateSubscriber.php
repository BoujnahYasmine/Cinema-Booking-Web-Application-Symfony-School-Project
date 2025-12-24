<?php

namespace App\EventSubscriber;

use App\Repository\ContactMessageRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class AdminTemplateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ContactMessageRepository $contactMessageRepository,
        private Environment $twig
    ) {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        
        // Only for admin routes
        if (str_starts_with($request->getPathInfo(), '/admin')) {
            $unreadCount = $this->contactMessageRepository->countUnread();
            $this->twig->addGlobal('unreadMessagesCount', $unreadCount);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}

