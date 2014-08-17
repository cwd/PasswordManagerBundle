<?php
/*
 * This file is part of the Password Manager Project.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AdmMgr\Model\Entity;

use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * AdmMgr\Model\Entity\Role
 *
 * @ORM\Entity(repositoryClass="AdmMgr\Model\Repository\RoleRepository")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Gedmo\Versioned
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Length(max = "100", groups={"default"})
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Gedmo\Versioned
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Length(max = "150", groups={"default"})
     */
    private $name;

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
     * @ORM\OneToMany(targetEntity="AdmMgr\Model\Entity\Role", mappedBy="childRoles")
     */
    private $parentRole;

    /**
     * @ORM\ManyToOne(targetEntity="AdmMgr\Model\Entity\Role", inversedBy="parentRole")
     * @ORM\JoinColumn(name="parentId", referencedColumnName="id")
     */
    private $childRoles;

    /**
     * @ORM\ManyToMany(targetEntity="AdmMgr\Model\Entity\User", inversedBy="roles", cascade={"persist","refresh"})
     * @ORM\JoinTable(
     *     name="UserHasRole",
     *     joinColumns={@ORM\JoinColumn(name="roleId", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=false)}
     * )
     */
    private $users;

    /**
     * Set Defaults
     */
    public function __construct()
    {
        $this->childRoles = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getChildRoles()
    {
        return $this->childRoles;
    }

    /**
     * @param Collection $childRoles
     *
     * @return $this
     */
    public function setChildRoles($childRoles)
    {
        $this->childRoles = $childRoles;

        return $this;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function addChildRole(Role $role)
    {
        $this->childRoles[] = $role;

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
    public function setCreatedAt(\Datetime $createdAt)
    {
        $this->createdAt = $createdAt;

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
    public function setDeletedAt(\Datetime $deletedAt)
    {
        $this->deletedAt = $deletedAt;

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
     * @return Role
     */
    public function getParentRole()
    {
        return $this->parentRole;
    }

    /**
     * @param Role $parentRole
     *
     * @return $this
     */
    public function setParentRole(Role $parentRole)
    {
        $this->parentRole = $parentRole;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

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