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
use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use PwdMgr\Model\Entity\User;
use PwdMgr\Service\Exception\UserNotFoundException as NotFoundException;
use PwdMgr\Model\Entity\Key as Entity;

/**
 * User Service Class
 *
 * @author Ludwig Ruderstaller <lr@cwd.at>
 * @DI\Service("service_key")
 * @SuppressWarnings("ShortVariable")
 */
class Key extends BaseService
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
     * @param User   $user
     * @param string $password
     *
     * @return Key
     */
    public function createKeys(User $user, $password)
    {
        $ssl = $this->getSSL();
        $ssl->generateKey($password);
        $key = new Entity();
        $key->setPublic($ssl->getPublicKey())
            ->setPrivate($ssl->getPrivateKey())
            ->setUser($user);
        $this->getService()->persist($key);

        return $key;
    }

    /**
     * Check Password against Key
     * @param Entity $key
     * @param string $password
     *
     * @return bool
     */
    public function checkKeyPassword(Entity $key, $password)
    {
        try {
            $this->getSSL()->openPrivateKey($key->getPrivate(), $password);
        } catch (\Exception $e) {
            $this->getLogger()->info($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param Entity $key
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return Entity
     * @throws \Exception
     */
    public function updatePasshrase($key, $oldPassword, $newPassword)
    {
        $ssl = $this->getSSL();
        $privateKey = $ssl->updatePassphrase($newPassword, $oldPassword, $key->getPrivate());
        $key->setPrivate($privateKey);

        return $key;
    }

    /**
     * Find Object by ID
     *
     * @param int $id
     *
     * @return Entity
     * @throws NotFoundException
     */
    public function find($id)
    {
        $obj = parent::findById('Model:Key', intval($id));

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
