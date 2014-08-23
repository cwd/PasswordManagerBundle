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

use Cwd\Bundle\SSLCryptBundle\Services\SSL;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use PwdMgr\Service\Exception\CategoryNotFoundException;
use PwdMgr\Service\Exception\UserNotFoundException as NotFoundException;
use PwdMgr\Model\Entity\Category as Entity;

/**
 * Category Service Class
 *
 * @author Ludwig Ruderstaller <lr@cwd.at>
 * @DI\Service("service_category")
 * @SuppressWarnings("ShortVariable")
 */
class Category extends BaseService
{
    /**
     * @param EntityManager $entityManager
     * @param Logger        $logger
     *
     * @DI\InjectParams({
     * })
     */
    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger        = $logger;
    }

    /**
     * @param string $slug
     *
     * @return Entity
     * @throws Exception\CategoryNotFoundException
     */
    public function findBySlug($slug)
    {
        $result = $this->getEm()->getRepository('Model:Category')->findBySlug($slug);
        if ($result) {
            return $result;
        }

        throw new CategoryNotFoundException();
    }

    /**
     * @param array $data
     */
    public function updateTree(array $data)
    {
        $this->runNodes($data);
        $result = $this->getEm()->getRepository('Model:Category')->verify();
        $this->getEm()->getRepository('Model:Category')->recover();
        $this->flush();

        return $result;
    }

    /**
     * Recursive Loop to update the Tree
     * @param array $nodes
     * @param null  $parentNode
     * @param int   $lvl
     * @param int   $lastLeft
     *
     * @throws Exception\UserNotFoundException
     */
    protected function runNodes($nodes, $parentNode = null, $lvl = 0, $lastLeft = 0)
    {
        foreach ($nodes as $idx => $n) {
            $lastLeft++;
            $id = $n->id;
            $node = $this->find($id);
            $node->setLft($idx + $lastLeft);
            $node->setLvl($lvl);
            if ($parentNode != null) {
                $node->setParent($parentNode);
            } else {
                $node->setParent(null);
            }

            if (isset($n->children) && count($n->children) > 0) {
                $level = $lvl + 1;
                $this->runNodes($n->children, $node, $level, $lastLeft);
            }
        }
    }

   /**
     * Find Object by ID
     *
     * @param int $id
     *
     * @return \PwdMgr\Model\Entity\Category
     * @throws NotFoundException
     */
    public function find($id)
    {
        $obj = parent::findById('Model:Category', intval($id));

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