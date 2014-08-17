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
use VMelnik\DoctrineEncryptBundle\Configuration\Encrypted;


/**
 * @ORM\Entity(repositoryClass="AdmMgr\Model\Repository\KeyRepository")
 * @ORM\Table(name="Keystore")
 * @Gedmo\Loggable
 */
class Key
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Gedmo\Versioned
     * @Encrypted
     */
    private $private;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Gedmo\Versioned
     * @Encrypted
     */
    private $public;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updateAt;

    /**
     * @ORM\OneToOne(targetEntity="AdmMgr\Model\Entity\User", inversedBy="key")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=false, unique=true)
     */
    private $user;

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
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * @param string $private
     *
     * @return $this
     */
    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * @param string $public
     *
     * @return $this
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return \Datetime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * @param \Datetime $updateAt
     *
     * @return $this
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }


}