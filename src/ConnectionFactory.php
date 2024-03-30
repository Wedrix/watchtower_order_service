<?php

declare(strict_types=1);

namespace App;

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory as DoctrineConnectionFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\TransactionIsolationLevel;

class ConnectionFactory
{
    public function __construct(
        private readonly DoctrineConnectionFactory $decorated
    ){}

    public function createConnection(
        array $params, 
        Configuration $config = null, 
        EventManager $eventManager = null, 
        array $mappingTypes = []
    ) {
        $connection = $this->decorated->createConnection($params, $config, $eventManager, $mappingTypes);
        
        $connection->setTransactionIsolation(TransactionIsolationLevel::REPEATABLE_READ);

        return $connection;
    }
}