<?php

declare(strict_types=1);

namespace App;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use GraphQL\Error\UserError;

class EntityManager implements EntityManagerInterface
{
    private bool $graphQLUserErrorOccurred = false;

    public function __construct(
        private readonly EntityManagerInterface $decorated
    ){}

    public function getMetadataFactory()
    {
        return $this->decorated->getMetadataFactory();
    }

    public function refresh($object, $lockMode = null)
    {
        $this->decorated->refresh($object, $lockMode);
    }

    public function getRepository($className)
    {
        return $this->decorated->getRepository($className);
    }

    public function getCache()
    {
        return $this->decorated->getCache();
    }

    public function getConnection()
    {
        return $this->decorated->getConnection();
    }

    public function getExpressionBuilder()
    {
        return $this->decorated->getExpressionBuilder();
    }

    public function beginTransaction()
    {
        $this->decorated->beginTransaction();
    }

    public function transactional($func)
    {
        return $this->decorated->transactional($func);
    }

    public function commit()
    {
        $this->decorated->commit();
    }

    public function rollback()
    {
        $this->decorated->rollback();
    }

    public function createQuery($dql = '')
    {
        return $this->decorated->createQuery($dql);
    }

    public function createNamedQuery($name)
    {
        return $this->decorated->createNamedQuery($name);
    }

    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {
        return $this->decorated->createNativeQuery($sql, $rsm);
    }

    public function createNamedNativeQuery($name)
    {
        return $this->decorated->createNamedNativeQuery($name);
    }

    public function createQueryBuilder()
    {
        return $this->decorated->createQueryBuilder();
    }

    public function getReference($entityName, $id)
    {
        return $this->decorated->getReference($entityName, $id);
    }

    public function getPartialReference($entityName, $identifier)
    {
        return $this->decorated->getPartialReference($entityName, $identifier);
    }

    public function close()
    {
        $this->decorated->close();
    }

    public function copy($entity, $deep = false)
    {
        return $this->decorated->copy($entity, $deep);
    }

    public function lock($entity, $lockMode, $lockVersion = null)
    {
        $this->decorated->lock($entity, $lockMode, $lockVersion);
    }

    public function getEventManager()
    {
        return $this->decorated->getEventManager();
    }

    public function getConfiguration()
    {
        return $this->decorated->getConfiguration();
    }

    public function isOpen()
    {
        return $this->decorated->isOpen();
    }

    public function getUnitOfWork()
    {
        return $this->decorated->getUnitOfWork();
    }

    public function getHydrator($hydrationMode)
    {
        return $this->decorated->getHydrator($hydrationMode);
    }

    public function newHydrator($hydrationMode)
    {
        return $this->decorated->newHydrator($hydrationMode);
    }

    public function getProxyFactory()
    {
        return $this->decorated->getProxyFactory();
    }

    public function getFilters()
    {
        return $this->decorated->getFilters();
    }

    public function isFiltersStateClean()
    {
        return $this->decorated->isFiltersStateClean();
    }

    public function hasFilters()
    {
        return $this->decorated->hasFilters();
    }

    public function getClassMetadata($className)
    {
        return $this->decorated->getClassMetadata($className);
    }

    public function find(string $className, $id)
    {
        return $this->decorated->find($className, $id);
    }

    public function persist(object $object)
    {
        $this->decorated->persist($object);
    }

    public function remove(object $object)
    {
        $this->decorated->remove($object);
    }

    public function clear()
    {
        $this->decorated->clear();
    }

    public function detach(object $object)
    {
        $this->decorated->detach($object);
    }

    public function flush()
    {
        $this->decorated->flush();
    }

    public function initializeObject(object $obj)
    {
        $this->decorated->initializeObject($obj);
    }

    public function contains(object $object)
    {
        return $this->decorated->contains($object);
    }
    
    public function wrapInTransaction(callable $func)
    {
        $connection = $this->getConnection();
        
        $connection->beginTransaction();

        try {
            $return = $func($this);

            if (($connection->getTransactionNestingLevel() == 1) && $this->graphQLUserErrorOccurred) {
                return $return;
            }

            $this->flush();

            $connection->commit();

            return $return;
        } catch (\Throwable $e) {
            if ($e instanceof UserError) {
                $this->graphQLUserErrorOccurred = true;
            }

            $this->close();

            $connection->rollBack();

            throw $e;
        }
    }
}