<?php
/*
 * This file is part of the Password Manager Project.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PwdMgr\Model\Entity;

use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PwdMgr\Model\Entity\Group
 *
 * @ORM\Entity(repositoryClass="PwdMgr\Model\Repository\GroupRepository")
 * @ORM\Table(name="`Group`")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 */
class Group
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Gedmo\Versioned
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Length(max = "200", groups={"default"})
     */
    private $name;

    /**
     * @var \Datetime
     * @ORM\Column(type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \Datetime
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var \Datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var Group
     * @ORM\OneToMany(targetEntity="PwdMgr\Model\Entity\Group", mappedBy="children")
     */
    private $parent;

    /**
     * @var Store
     * @ORM\OneToMany(targetEntity="PwdMgr\Model\Entity\Store", mappedBy="group")
     */
    private $stores;

    /**
     * @var Collection
     * @ORM\ManyToOne(targetEntity="PwdMgr\Model\Entity\Group", inversedBy="parent")
     * @ORM\JoinColumn(name="parentId", referencedColumnName="id")
     */
    private $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Collection $children
     *
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \Datetime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Group
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Group $parent
     *
     * @return $this
     */
    public function setParent(Group $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @param Store $store
     *
     * @return $this
     */
    public function addStore(Store $store)
    {
        $this->stores[] = $store;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * @param Collection $stores
     *
     * @return $this
     */
    public function setStores($stores)
    {
        $this->stores = $stores;

        return $this;
    }

    /**
     * @param Store $store
     *
     * @return $this
     */
    public function removeStore(Store $store)
    {
        $this->getStores()->removeElement($store);

        return $this;
    }

    /**
     * @return \Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \Datetime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \Datetime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \Datetime $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }


}