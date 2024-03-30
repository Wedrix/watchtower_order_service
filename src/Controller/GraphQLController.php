<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Service\AuthService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Error\DebugFlag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wedrix\WatchtowerBundle\Controller\WatchtowerController;
use Wedrix\Watchtower\Executor as WatchtowerExecutor;

class GraphQLController extends WatchtowerController
{   
    /**
     * @param array<string,mixed> $context
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly WatchtowerExecutor $executor,
        private readonly UserService $userService,
        private readonly AuthService $authService,
        private readonly OrderRepository $orderRepository
    ){}

    public function __invoke(
        Request $request
    ): Response
    {
        $input = \json_decode($request->getContent(), true);

        $response = new Response();

        $response->setContent(
            content: \is_string(
                $responseBody = \json_encode(
                    $this->entityManager->wrapInTransaction(
                        fn() => $this->executor
                                    ->executeQuery(
                                        source: $input['query'] ?? '',
                                        rootValue: [],
                                        contextValue: [
                                            'user_service' => $this->userService,
                                            'auth_service' => $this->authService,
                                            'order_repository' => $this->orderRepository
                                        ],
                                        variableValues: $input['variables'] ?? null,
                                        operationName: $input['operationName'] ?? null,
                                        validationRules: null
                                    )
                                    ->toArray(
                                        debug: DebugFlag::RETHROW_UNSAFE_EXCEPTIONS
                                    )
                    )
                )
            ) 
            ? $responseBody
            : throw new \Exception('Unable to encode GraphQL result')
        )
        ->headers
        ->set('Content-Type', 'application/json; charset=utf-8');

        return $response;
    }
}