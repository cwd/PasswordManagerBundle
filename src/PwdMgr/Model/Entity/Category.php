<?php
namespace PwdMgr\Model\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Oneup\AclBundle\Mapping\Annotation as Acl;


/**
 * PwdMgr\Model\Entity\Category
 *
 * @Gedmo\Loggable
 * @Gedmo\Tree(type="nested")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 * @ORM\Entity(repositoryClass="PwdMgr\Model\Repository\CategoryRepository")
 * @ORM\Table(name="Category")
 * @Acl\DomainObject({
 *   @Acl\ClassPermission({ "ROLE_ADMIN" = MaskBuilder::MASK_IDDQD })
 * })
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Gedmo\Versioned
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Length(max = "200", groups={"default"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", unique=true, length=128, nullable=false)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\TreeRoot
     */
    private $root;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\TreeRight
     */
    private $rgt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\TreeLevel
     */
    private $lvl;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\TreeLeft
     */
    private $lft;

    /**
     * @ORM\OneToMany(targetEntity="PwdMgr\Model\Entity\Category", mappedBy="parent")
     * @ORM\OrderBy({"lft"="ASC"})
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="PwdMgr\Model\Entity\Store", mappedBy="category")
     */
    private $stores;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="PwdMgr\Model\Entity\Category", inversedBy="children")
     * @ORM\JoinColumn(name="parentId", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     *
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param mixed $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * @param mixed $lft
     *
     * @return $this
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * @param mixed $lvl
     *
     * @return $this
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     *
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * @param mixed $rgt
     *
     * @return $this
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param mixed $root
     *
     * @return $this
     */
    public function setRoot($root)
    {
        $this->root = $root;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * @param mixed $stores
     *
     * @return $this
     */
    public function setStores($stores)
    {
        $this->stores = $stores;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }


}
