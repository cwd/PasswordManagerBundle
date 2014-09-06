<?php
/*
 * This file is part of password-manager.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PwdMgr\Model\Repository;

use Doctrine\ORM\EntityManager;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Cwd\GenericBundle\Repository\RepositoryUtils as CwdRepositoryUtils;
use PwdMgr\Model\Entity\Category;

/**
 * Group Repository
 *
 * @author Ludwig Ruderstaller <lr@cwd.at>
 * @SuppressWarnings("ShortVariable")
 */
class CategoryRepository extends NestedTreeRepository
{
    /**
     * @param EntityManager $em
     * @param ClassMetadata $class
     */
    public function __construct(EntityManager $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->repoUtils = new CwdRepositoryUtils($this->_em, $this->getClassMetadata(), $this->listener, $this);
    }

    /**
     * @param string $slug
     *
     * @return Category
     */
    public function findBySlug($slug)
    {
        $q = $this->createQueryBuilder('c')
            ->select('c')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery();

        return $q->getSingleResult();
    }
}
