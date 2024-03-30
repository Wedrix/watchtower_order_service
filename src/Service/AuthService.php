<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Session;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GraphQL\Error\UserError;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthService
{
    private ?Session $session;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ContainerBagInterface $params,
        private readonly UserRepository $userRepository,
        private readonly SessionRepository $sessionRepository
    )
    {
        $this->session = null;
    }

    public function session(): Session
    {
        if (\is_null($this->session)) {
            throw new \Exception('The session is unset.');
        }

        return $this->session;
    }

    public function hasSession(): bool
    {
        return !\is_null($this->session);
    }

    public function token(): ?string
    {
        if (\is_null($this->session)) {
            return null;
        }

        return JWT::encode(
            payload: [
                'iss' => $this->params->get('app.domain'),
                'aud' => $this->params->get('app.domain'),
                'iat' => ($time = \date_create_immutable('now'))->getTimestamp(),
                'exp' => $time->modify('+'.$this->params->get('auth.token_ttl_minutes').' minutes')->getTimestamp(),
                'sub' => $this->session->user()->id(),
                'session_id' => $this->session->id()
            ],
            key: $this->params->get('auth.token_signing_key'),
            alg: $this->params->get('auth.token_signing_algorithm')
        );
    }

    public function signIn(
        User $user,
        string $password
    ): void
    {
        if (!\is_null($this->session)) {
            throw new \Exception('The session is set.');
        }

        if (!\password_verify($password, $user->password())) {
            throw new UserError('Wrong email or password!');
        }

        $session = new Session($user);

        $this->entityManager->persist($session);

        $this->session = $session;
    }

    public function signOut(): void
    {
        if (\is_null($this->session)) {
            throw new \Exception('The session is unset.');
        }

        $this->entityManager->remove($this->session);

        $this->session = null;
    }

    public function load(
        Request $request
    ): void
    {
        if (!\is_null($this->session)) {
            throw new \Exception('The session is set.');
        }

        $token = $request->headers->get($this->params->get('auth.token_header'));

        try {
            $tokenPayload = (array) JWT::decode(
                $token, 
                new Key(
                    $this->params->get('auth.token_signing_key'), 
                    $this->params->get('auth.token_signing_algorithm')
                )
            );
        }
        catch (\Exception) {
            return;
        }

        if (
            (!isset($tokenPayload['iss']))
            || (!isset($tokenPayload['aud']))
            || (!isset($tokenPayload['iat']))
            || (!isset($tokenPayload['exp']))
            || (!isset($tokenPayload['sub']))
            || (!isset($tokenPayload['session_id']))
            || ($tokenPayload['iss'] !== $this->params->get('app.domain'))
            || ($tokenPayload['aud'] !== $this->params->get('app.domain'))
            || ($tokenPayload['iat'] > ($time = \date_create_immutable('now'))->getTimestamp())
            || ($tokenPayload['exp'] !== $time->setTimestamp($tokenPayload['iat'] + ($this->params->get('auth.token_ttl_minutes') * 60))->getTimestamp())
        ) {
            return;
        }

        if ($this->userRepository->hasUserWithId($tokenPayload['sub'])) {
            $user = $this->userRepository->userWithId($tokenPayload['sub']);
    
            if ($this->sessionRepository->hasSessionOfUserWithId($user, $tokenPayload['session_id'])) {
                $this->session = $this->sessionRepository->sessionOfUserWithId($user, $tokenPayload['session_id']);
            }
        }
    }
}