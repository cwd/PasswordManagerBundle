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
use PwdMgr\Model\Entity\Key;
use PwdMgr\Service\Exception\UserNotFoundException as NotFoundException;
use PwdMgr\Model\Entity\User as Entity;

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
     * @var SSL
     */
    protected $ssl;

    /**
     * @param EntityManager $entityManager
     * @param Logger        $logger
     * @param SSL           $ssl
     *
     * @DI\InjectParams({
     *      "ssl" = @DI\Inject("cwd.bundle.sslcrypt.ssl")
     * })
     */
    public function __construct(EntityManager $entityManager, Logger $logger, SSL $ssl)
    {
        $this->entityManager = $entityManager;
        $this->logger        = $logger;
        $this->ssl           = $ssl;
    }

    /**
     * Create new Keypair for given User with given Password
     *
     * @param Entity $user
     * @param string $password
     *
     * @return Key
     */
    public function createKeys(Entity $user, $password)
    {
        $ssl = $this->getSSL();
        $ssl->generateKey($password);
        $key = new Key();
        $key->setPublic($ssl->getPublicKey())
            ->setPrivate($ssl->getPrivateKey())
            ->setUser($user);
        $this->getService()->persist($key);

        return $key;
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
     * @return \PwdMgr\Model\Entity\User
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
     * Get SSL Service Instance
     * @return SSL
     */
    protected function getSSL()
    {
        return $this->ssl;
    }

    /**
     * @return Entity
     */
    public function getNew()
    {
        return new Entity();
    }
}