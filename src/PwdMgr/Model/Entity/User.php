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
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints as RollerworksPassword;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * PwdMgr\Model\Entity\User
 *
 * @ORM\Entity(repositoryClass="PwdMgr\Model\Repository\UserRepository")
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 */
class User implements UserInterface
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
    private $firstname;

    /**
     * @ORM\Column(type="string", length=150, nullable=false)
     * @Gedmo\Versioned
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Length(max = "150", groups={"default"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=150, nullable=false)
     * @Gedmo\Versioned
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Length(max = "150", groups={"default"})
     * @Assert\Email(groups={"default"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(groups={"password"})
     * @RollerworksPassword\PasswordStrength(minLength=8, minStrength=4)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToOne(targetEntity="PwdMgr\Model\Entity\Key", mappedBy="user")
     */
    private $key;

    /**
     * @ORM\OneToMany(targetEntity="PwdMgr\Model\Entity\Store", mappedBy="owner")
     */
    private $ownerStores;

    /**
     * @ORM\ManyToMany(targetEntity="PwdMgr\Model\Entity\Store", mappedBy="users")
     */
    private $stores;

    /**
     * @ORM\ManyToMany(targetEntity="PwdMgr\Model\Entity\Role", mappedBy="users")
     * @Assert\NotBlank(groups={"default"})
     */
    private $roles;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * setup entity
     */
    public function __cosntruct()
    {
        $this->roles  = new ArrayCollection();
        $this->stores = new ArrayCollection();
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 13));

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     *
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

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
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     *
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getOwnerStores()
    {
        return $this->ownerStores;
    }

    /**
     * @param Collection $ownerStore
     *
     * @return $this
     */
    public function setOwnerStores($ownerStores)
    {
        $this->ownerStores = $ownerStores;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getRolesCollection()
    {
        return $this->roles;
    }

    /**
     * Roles Array
     * @return array
     */
    public function getRoles()
    {
        $r = array();
        $roles = $this->getRolesCollection();
        foreach ($roles as $role) {
            $r[] = $role->getRole();
        }

        return $r;
    }

    /**
     * @param Collection $roles
     *
     * @return $this
     */
    public function setRolesCollection($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function addRolesCollection(Role $role)
    {
        $this->roles[] = $role;
        $role->addUser($this);

        return $this;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function removeRolesCollection(Role $role)
    {
        $this->getRoles()->removeElement($role);

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
    public function addStore(Store $store)
    {
        $this->stores[] = $store;

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
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param Key $key
     *
     * @return $this
     */
    public function setKey(Key $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }
}