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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VMelnik\DoctrineEncryptBundle\Configuration\Encrypted;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Oneup\AclBundle\Mapping\Annotation as Acl;

/**
 * Store Entity
 *
 * @ORM\Entity(repositoryClass="PwdMgr\Model\Repository\StoreRepository")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 * @Acl\DomainObject({
 *   @Acl\ClassPermission({ "ROLE_ADMIN" = MaskBuilder::MASK_IDDQD })
 * })
 */
class Store
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Length(max = "200", groups={"default"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Length(max = "200", groups={"default"})
     */
    private $username;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Encrypted
     */
    private $envkey;

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
     * @ORM\ManyToOne(targetEntity="PwdMgr\Model\Entity\Category", inversedBy="stores")
     * @ORM\JoinColumn(name="groupId", referencedColumnName="id", nullable=false)
     */
    private $category;

    /**
     * 
     * 
     */
    private $group;

    /**
     * @ORM\ManyToOne(targetEntity="PwdMgr\Model\Entity\User", inversedBy="ownerStores")
     * @ORM\JoinColumn(name="ownerId", referencedColumnName="id", nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity="PwdMgr\Model\Entity\User", inversedBy="stores")
     * @ORM\JoinTable(
     *     name="UserHasStore",
     *     joinColumns={@ORM\JoinColumn(name="storeId", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=false)}
     * )
     */
    private $users;

    /**
     * Setup object
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

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

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Group $group
     *
     * @return $this
     */
    public function setGroup($group)
    {
        $this->group = $group;

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
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     *
     * @return $this
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     *
     * @return $this
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnvkey()
    {
        return $this->envkey;
    }

    /**
     * @param string $envkey
     *
     * @return $this
     */
    public function setEnvkey($envkey)
    {
        $this->envkey = $envkey;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param Collection $users
     *
     * @return $this
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function removeUser(User $user)
    {
        $this->getUsers()->removeElement($user);

        return $this;
    }
}
