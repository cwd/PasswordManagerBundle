<?php
/*
 * This file is part of the Password Manager.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PwdMgr\Service;

use Cwd\GenericBundle\Exception\PersistanceException;
use Doctrine\Common\Collections\Collection;
use Cwd\GenericBundle\BaseService as CwdService;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;

/**
 * Base Service Class
 *
 * @author Ludwig Ruderstaller <lr@cwd.at>
 * @SuppressWarnings("ShortVariable")
 */
abstract class BaseService extends CwdService
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param int    $id
     * @param string $model
     *
     * @return object
     * @deprecated Use findById instead!
     */
    public function findByModel($id, $model)
    {
        return $this->findById($model, $id);
    }

    /**
     * Get All from given Model
     * @param string $model
     *
     * @return Collection
     */
    public function findAllByModel($model)
    {
        return $this->getEm()->getRepository($model)->findAll();
    }

    /**
     * @param string $model
     * @param array  $where
     *
     * @return integer
     */
    public function getCountByModel($model, $where = array())
    {
        $qb = $this->getEm()->createQueryBuilder();
        $qb->select($qb->expr()->count('x'))
            ->from($model, 'x');

        if (count($where) > 0) {
            $qb->andWhere($where);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param string $model
     * @param int    $id
     *
     * @return object
     */
    public function findById($model, $id)
    {
        return $this->entityManager->getRepository($model)->find($id);
    }

    /**
     * Find Entities by fields in given Model
     *
     * @param string $model
     * @param array  $filter
     * @param array  $sort
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     */
    public function findByFilter($model, $filter = array(), $sort = array(), $limit = null, $offset = null)
    {
        return $this->getEm()->getRepository($model)->findby($filter, $sort, $limit, $offset);
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param misc $object
     *
     * @throws PersistanceException
     *
     * @return bool
     */
    public function persist($object)
    {
        try {
            $this->entityManager->persist($object);
        } catch (\Exception $e) {
            $this->getLogger()->warn('Object could not be saved', (array) $e);
            throw new PersistanceException('Object could not be stored - '.$e->getMessage(), null, $e);
        }

        return true;
    }

    public function flush()
    {
        $this->getEm()->flush();
    }

    /**
     * @param int $id
     *
     * @throws NotFoundException
     * @return true
     */
    public function remove($id)
    {
        $object = $this->find($id);
        $this->getEm()->remove($object);
        $this->getEm()->flush();

        return true;
    }

    /**
     * @param int $id
     *
     * @throws NotFoundException
     * @return true
     */
    public function restore($id)
    {
        $this->getEm()->getFilters()->disable('softdeleteable');
        $object = $this->find($id);
        $object->setDeletedAt(null);
        $this->getEm()->flush();

        return true;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->entityManager;
    }
}
