<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthEventsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AuthService $auth,
        private readonly ContainerBagInterface $params
    ){}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
            KernelEvents::RESPONSE => 'onResponse'
        ];
    }

    public function onRequest(
        RequestEvent $requestEvent
    )
    {
        $request = $requestEvent->getRequest();

        if ($request->headers->has($this->params->get('auth.token_header')) && !$this->auth->hasSession()) {
            $this->auth->load($request);
        }
    }

    public function onResponse(
        ResponseEvent $responseEvent
    )
    {
        if ($this->auth->hasSession() && $this->auth->session()->isNew()) {
            $response = $responseEvent->getResponse();

            $response->headers->set($this->params->get('auth.token_header'), $this->auth->token());
        }
    }
}