<?php
/*
 * This file is part of the Password Manager.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AdmMgr\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Session\Session;
use AdmMgr\Service\Exception\UserNotFoundException as NotFoundException;
use AdmMgr\Model\Entity\User as Entity;

/**
 * User Service Class
 *
 * @author Ludwig Ruderstaller <lr@cwd.at>
 * @DI\Service("service_user")
 * @SuppressWarnings("ShortVariable")
 */
class User extends BaseService
{
    /**
     * @param EntityManager $entityManager
     * @param Logger        $logger
     *
     * @DI\InjectParams()
     */
    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->entityManager     = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @return ArrayCollection
     */
    public function fetchAll()
    {
        return $this->entityManager->getRepository('Model:User')->findby(array(), array('name' => 'ASC'));
    }

    /**
     * @param array $where
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCount($where = array())
    {
        return parent::getCountByModel('Model:User', $where);
    }

    /**
     * Find Object by ID
     *
     * @param int $id
     *
     * @return \AdmMgr\Model\Entity\User
     * @throws NotFoundException
     */
    public function find($id)
    {
        $obj = parent::findById('Model:User', intval($id));

        if ($obj === null) {
            $this->getLogger()->info('Row with ID {id} not found', array('id' => $id));
            throw new NotFoundException('Row with ID '.$id.' not found');
        }

        return $obj;
    }

    /**
     * @return Entity
     */
    public function getNew()
    {
        return new Entity();
    }
}