<?php

namespace App\EventSubscriber;

//use ApiPlatform\Core\EventListener\EventPriorities;
//use ApiPlatform\Core\EventListener\WriteListener;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Note;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\ORM\EntityManagerInterface;

class LinkUserToNoteSubscriber implements EventSubscriberInterface
{
    private $requestStack;
    private $entityManager;
    private LoggerInterface $logger;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                ['linkUserToNote', EventPriorities::PRE_WRITE],
                ['updateSession', EventPriorities::POST_WRITE],
            ],
        ];
    }


    public function updateSession(ViewEvent $event)
    {
        $note = $event->getControllerResult();
        $session = $event->getRequest()->getSession();
        $session->set('user_id', $note->getUser()->getId());
    }

    public function linkUserToNote(ViewEvent $event)
    {


        $note = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        if (!$note instanceof Note || Request::METHOD_POST !== $method) {
            return;
        }

        //$session = $this->requestStack->getSession();
        $session = $event->getRequest()->getSession();
        $sessionId = $session->getId();

        $this->logger->info("linkUserToNote", [
            'event' => $event->getRequest(),
            'session_id' => $sessionId,
        ]);


        //get user id from session:
        $userId = $session->get("user_id");

        $user = $userId ? $this->entityManager->getRepository(User::class)->find($userId) : new User();

        $note->setUser($user);

        // Persist the user if it's new
        if (!$user->getId()) {
            $this->entityManager->persist($user);
        }

    }
}
