<?php

declare(strict_types=1);

namespace App\Service;

use App\DataType\EmailAddress;
use App\DataType\Name;
use App\DataType\Password;
use App\DataType\Price;
use App\DataType\ProductName;
use App\DataType\Role;
use App\DataType\Stock;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductLine;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Error\UserError;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AuthService $auth,
        private readonly UserRepository $userRepository,
        private readonly ProductRepository $productRepository,
        private readonly OrderRepository $orderRepository
    ){}

    public function signUp(
        Name $name,
        EmailAddress $email,
        Password $password,
        Role $role
    ): User
    {
        return $this->entityManager
            ->wrapInTransaction(
                function() use($name,$email,$password,$role): User {
                    if ($this->auth->hasSession()) {
                        throw new UserError('A user is signed in.');
                    }

                    if ($this->userRepository->hasUserWithEmail($email)) {
                        throw new UserError("There is a user with email '$email'.");
                    }

                    $user = new User(
                        name: (string) $name,
                        email: (string) $email,
                        password: \password_hash((string) $password, \PASSWORD_BCRYPT),
                        role: (string) $role
                    );

                    $this->entityManager->persist($user);

                    $this->auth->signIn($user, (string) $password);

                    return $user;
                }
            );
    }

    public function signIn(
        EmailAddress $email,
        Password $password
    ): User
    {
        return $this->entityManager
            ->wrapInTransaction(
                function() use($email, $password): User {
                    if ($this->auth->hasSession()) {
                        throw new UserError('A user is signed in.');
                    }

                    if (!$this->userRepository->hasUserWithEmail($email)) {
                        throw new UserError('Wrong email or password!');
                    }

                    $user = $this->userRepository->userWithEmail($email);
                    
                    $this->auth->signIn($user, (string) $password);

                    return $user;
                }
            );
    }

    public function signOut(): bool
    {
        return $this->entityManager
            ->wrapInTransaction(
                function(): bool {
                    if (!$this->auth->hasSession()) {
                        throw new UserError('Unauthorized! Sign in to proceed.');
                    }

                    $this->auth->signOut();

                    return true;
                }
            );
    }

    public function createUser(
        Name $name,
        EmailAddress $email,
        Password $password,
        Role $role
    ): User
    {
        return $this->entityManager
            ->wrapInTransaction(
                function() use($name, $email, $password, $role): User {
                    if (!$this->auth->hasSession()) {
                        throw new UserError('Unauthorized! Sign in to proceed.');
                    }

                    $authUser = $this->auth->session()->user();

                    if ($authUser->role() !== 'ROLE_ADMIN') {
                        throw new UserError('Unauthorized! Only Admins can create users.');
                    }
                    
                    if ($this->userRepository->hasUserWithEmail($email)) {
                        throw new UserError("There is a user with email '$email'.");
                    }

                    $user = new User(
                        name: (string) $name,
                        email: (string) $email,
                        password: \password_hash((string) $password, \PASSWORD_BCRYPT),
                        role: (string) $role
                    );

                    $this->entityManager->persist($user);

                    return $user;
                }
            );
    }

    public function updateUser(
        int $userId,
        ?Name $name,
        ?EmailAddress $email,
        ?Password $password,
        ?Role $role
    ): User
    {
        return $this->entityManager
            ->wrapInTransaction(
                function() use($userId, $name, $email, $password, $role): User {
                    if (!$this->auth->hasSession()) {
                        throw new UserError('Unauthorized! Sign in to proceed.');
                    }

                    $authUser = $this->auth->session()->user();

                    if (!$this->userRepository->hasUserWithId($userId)) {
                        throw new UserError("There is no user with id '$userId'.");
                    }

                    $user = $this->userRepository->userWithId($userId);

                    if (($authUser->id() !== $user->id()) && ($authUser->role() !== 'ROLE_ADMIN')) {
                        throw new UserError('Unauthorized! Only Admins can update other users.');
                    }

                    if (!\is_null($role)) {
                        if ($authUser->role() !== 'ROLE_ADMIN') {
                            throw new UserError('Unauthorized! Only Admins can update user roles.');
                        }

                        $user->setRole((string) $role);
                    }

                    if (!\is_null($email)) {
                        if ($this->userRepository->hasUserWithEmail($email)) {
                            throw new UserError("There is a user with email '$email'.");
                        }

                        $user->setEmail((string) $email);
                    }

                    if (!\is_null($name)) {
                        $user->setName((string) $name);
                    }

                    if (!\is_null($password)) {
                        $user->setPassword(\password_hash((string) $password, \PASSWORD_BCRYPT));
                    }

                    return $user;
                }
            );
    }

    public function deleteUser(
        int $userId
    ): bool
    {
        return $this->entityManager
            ->wrapInTransaction(
                function() use($userId): bool {
                    if (!$this->auth->hasSession()) {
                        throw new UserError('Unauthorized! Sign in to proceed.');
                    }

                    $authUser = $this->auth->session()->user();

                    if (!$this->userRepository->hasUserWithId($userId)) {
                        throw new UserError("There is no user with id '$userId'.");
                    }

                    $user = $this->userRepository->userWithId($userId);

                    if (($authUser->id() !== $user->id()) && ($authUser->role() !== 'ROLE_ADMIN')) {
                        throw new UserError('Unauthorized! Only Admins can delete other users.');
                    }

                    $this->entityManager->remove($user);

                    return true;
                }
            );
    }

    /**
     * @param array<int,array<string,int|string>> $productLines 
     */
    public function createOrder(
        int $userId,
        array $productLines
    ): Order
    {
        return $this->entityManager
            ->wrapInTransaction(
                function() use($userId, $productLines): Order {
                    if (!$this->auth->hasSession()) {
                        throw new UserError('Unauthorized! Sign in to proceed.');
                    }

                    $authUser = $this->auth->session()->user();

                    if (!$this->userRepository->hasUserWithId($userId)) {
                        throw new UserError("There is no user with id '$userId'.");
                    }

                    $user = $this->userRepository->userWithId($userId);

                    if (($authUser->id() !== $user->id()) && ($authUser->role() !== 'ROLE_ADMIN')) {
                        throw new UserError('Unauthorized! Only Admins can create orders for other users.');
                    }

                    $order = new Order(
                        user: $user,
                        productLines: $productLines = new class(
                            productLines: $productLines,
                            productRepository: $this->productRepository,
                            order: static function() use(&$order) {
                                return $order;
                            }
                        ) extends AbstractLazyCollection implements Selectable {
                            /**
                             * @param array<int,array<string,mixed>> $productLine 
                             */
                            public function __construct(
                                private readonly array $productLines,
                                private readonly ProductRepository $productRepository,
                                private readonly \Closure $order
                            ){}
                        
                            protected function doInitialize(): void
                            {
                                $this->collection = new ArrayCollection(
                                    \array_map(
                                        function(array $productLine): ProductLine {
                                            if (!$this->productRepository->hasProductWithId($productId = (int) $productLine['productId'])) {
                                                throw new UserError("There is no product with id '$productId'.");
                                            }
    
                                            return new ProductLine(
                                                order: ($this->order)(),
                                                product: $this->productRepository->productWithId($productId),
                                                quantity: $productLine['quantity']
                                            );
                                        },
                                        $this->productLines
                                    )
                                );
                            }

                            public function matching(
                                Criteria $criteria
                            ): ReadableCollection
                            {
                                /**
                                 * @var ArrayCollection
                                 */
                                $collection = $this->collection;

                                return $collection->matching($criteria);
                            }
                        }
                    );

                    foreach ($productLines as $productLine) {
                        $product = $productLine->product();

                        if ($product->stock() < $productLine->quantity()) {
                            throw new UserError("The product '{$product->name()}' of id '{$product->id()}' has insufficient stock to fulfil the order.");
                        }

                        $product->setStock($product->stock() - $productLine->quantity());
                    }

                    $this->entityManager->persist($order);

                    return $order;
                }
            );
    }

    /**
     * @param array<int,array<string,int|string>>|null $productLines 
     */
    public function updateOrder(
        int $orderId,
        ?int $userId,
        ?array $productLines
    ): Order
    {
        return $this->entityManager
            ->wrapInTransaction(
                function() use($orderId, $userId, $productLines): Order {
                    if (!$this->auth->hasSession()) {
                        throw new UserError('Unauthorized! Sign in to proceed.');
                    }

                    $authUser = $this->auth->session()->user();

                    if (!$this->orderRepository->hasOrderWithId($orderId)) {
                        throw new UserError("There is no order with id '$orderId'.");
                    }

                    $order = $this->orderRepository->orderWithId($orderId);

                    if (($authUser->id() !== $order->user()->id()) && ($authUser->role() !== 'ROLE_ADMIN')) {
                        throw new UserError('Unauthorized! Only Admins can update other users\' orders.');
                    }

                    if (!\is_null($userId)) {
                        if ($authUser->role() !== 'ROLE_ADMIN') {
                            throw new UserError('Unauthorized! Only Admins can change the users of orders.');
                        }
    
                        if (!$this->userRepository->hasUserWithId($userId)) {
                            throw new UserError("There is no user with id '$userId'.");
                        }
    
                        $user = $this->userRepository->userWithId($userId);

                        $order->setUser($user);
                    }

                    if (!\is_null($productLines)) {
                        // Restore old productlines' stock and remove them
                        foreach ($order->productLines() as $oldProductLine) {
                            $product = $oldProductLine->product();
    
                            $product->setStock($product->stock() + $oldProductLine->quantity());
    
                            $this->entityManager->remove($oldProductLine);
                        }
    
                        $order->productLines()->clear();
    
                        $this->entityManager->flush();
    
                        // Add new product lines to order and update their products' stock
                        $productLines = new ArrayCollection(
                            \array_map(
                                function(array $productLine) use($order): ProductLine {
                                    if (!$this->productRepository->hasProductWithId($productId = (int) $productLine['productId'])) {
                                        throw new UserError("There is no product with id '$productId'.");
                                    }
                                    
                                    return new ProductLine(
                                        order: $order,
                                        product: $this->productRepository->productWithId($productId),
                                        quantity: $productLine['quantity']
                                    );
                                },
                                $productLines
                            )
                        );
    
                        foreach ($productLines as $productLine) {
                            $product = $productLine->product();
    
                            if ($product->stock() < $productLine->quantity()) {
                                throw new UserError("The product '{$product->name()}' of id '{$product->id()}' has insufficient stock to fulfil the order.");
                            }
    
                            $product->setStock($product->stock() - $productLine->quantity());
    
                            $order->productLines()->add($productLine);
                        }
                    }

                    return $order;
                }
            );
    }

    public function deleteOrder(
        int $orderId
    ): bool
    {
        return $this->entityManager
            ->wrapInTransaction(
                function() use($orderId): bool {
                    if (!$this->auth->hasSession()) {
                        throw new UserError('Unauthorized! Sign in to proceed.');
                    }

                    $authUser = $this->auth->session()->user();

                    if (!$this->orderRepository->hasOrderWithId($orderId)) {
                        throw new UserError("There is no order with id '$orderId'.");
                    }

                    $order = $this->orderRepository->orderWithId($orderId);

                    if (($authUser->id() !== $order->user()->id()) && ($authUser->role() !== 'ROLE_ADMIN')) {
                        throw new UserError('Unauthorized! Only Admins can delete other users\' orders.');
                    }

                    // Restore old productlines' stock and remove them
                    foreach ($order->productLines() as $oldProductLine) {
                        $product = $oldProductLine->product();

                        $product->setStock($product->stock() + $oldProductLine->quantity());
                    }

                    $this->entityManager->remove($order);

                    return true;
                }
            );
    }
    
    public function createProduct(
        ProductName $name,
        Stock $stock,
        Price $price
    ): Product
    {
        return $this->entityManager
            ->wrapInTransaction(
                function() use($name, $stock, $price): Product {
                    if (!$this->auth->hasSession()) {
                        throw new UserError('Unauthorized! Sign in to proceed.');
                    }

                    $authUser = $this->auth->session()->user();

                    if ($authUser->role() !== 'ROLE_ADMIN') {
                        throw new UserError('Unauthorized! Only Admins can create products.');
                    }

                    $product = new Product(
                        name: (string) $name,
                        stock: (int) (string) $stock,
                        price: (string) $price
                    );

                    $this->entityManager->persist($product);

                    return $product;
                }
            );
    }

    public function updateProduct(
        int $productId,
        ?ProductName $name,
        ?Stock $stock,
        ?Price $price
    ): Product
    {
        return $this->entityManager
            ->wrapInTransaction(
                function() use($productId, $name, $stock, $price): Product {
                    if (!$this->auth->hasSession()) {
                        throw new UserError('Unauthorized! Sign in to proceed.');
                    }

                    $authUser = $this->auth->session()->user();

                    if ($authUser->role() !== 'ROLE_ADMIN') {
                        throw new UserError('Unauthorized! Only Admins can update products.');
                    }

                    if (!$this->productRepository->hasProductWithId($productId)) {
                        throw new UserError("There is no product with id '$productId'.");
                    }

                    $product = $this->productRepository->productWithId($productId);

                    if (!\is_null($name)) {
                        $product->setName((string) $name);
                    }

                    if (!\is_null($stock)) {
                        $product->setStock((int) (string) $stock);
                    }

                    if (!\is_null($price)) {
                        $product->setPrice((string) $price);
                    }

                    return $product;
                }
            );
    }

    public function deleteProduct(
        int $productId
    ): bool
    {
        return $this->entityManager
            ->wrapInTransaction(
                function() use($productId): bool {
                    if (!$this->auth->hasSession()) {
                        throw new UserError('Unauthorized! Sign in to proceed.');
                    }

                    $authUser = $this->auth->session()->user();

                    if ($authUser->role() !== 'ROLE_ADMIN') {
                        throw new UserError('Unauthorized! Only Admins can delete products.');
                    }

                    if (!$this->productRepository->hasProductWithId($productId)) {
                        throw new UserError("There is no product with id '$productId'.");
                    }

                    $product = $this->productRepository->productWithId($productId);

                    $this->entityManager->remove($product);

                    return true;
                }
            );
    }
}