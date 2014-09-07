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
use Oneup\AclBundle\Security\Acl\Manager\AclManager;
use PwdMgr\Model\Entity\Key as KeyEntity;
use PwdMgr\Service\Key as KeyService;
use PwdMgr\Service\Exception\UserNotFoundException as NotFoundException;
use PwdMgr\Model\Entity\User as Entity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

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
     * @var KeyService
     */
    protected $serviceKey;

    /**
     * @param EntityManager $entityManager
     * @param Logger        $logger
     * @param KeyService    $serviceKey
     *
     * @DI\InjectParams({
     *      "serviceKey" = @DI\Inject("service_key")
     * })
     */
    public function __construct(EntityManager $entityManager, Logger $logger, KeyService $serviceKey)
    {
        $this->entityManager = $entityManager;
        $this->logger        = $logger;
        $this->serviceKey    = $serviceKey;
    }

    /**
     * Create new Keypair for given User with given Password
     *
     * @param Entity $user
     * @param string $password
     *
     * @return KeyEntity
     */
    public function createKeys(Entity $user, $password)
    {
        return $this->getKeyService()->createKeys($user, $password);
    }

    /**
     * Check Password against Key
     * @param Entity $user
     * @param string $password
     *
     * @return bool
     */
    public function checkKeyPassword(Entity $user, $password)
    {
        return $this->getKeyService()->checkKeyPassword($user->getKey(), $password);
    }

    /**
     * @param Entity $user
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return Key
     * @throws \Exception
     */
    public function updatePasshrase(Entity $user, $oldPassword, $newPassword)
    {
        return $this->getKeyService()->updatePasshrase($user->getKey(), $oldPassword, $newPassword);
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
     * @param Entity     $user
     * @param array      $permission
     * @param AclManager $aclManager
     */
    public function setGlobalPermission(Entity $user, array $permission, AclManager $aclManager)
    {
        $this->getLogger()->addInfo(print_r($permission, 1));
        $keys = array('category' => 'PwdMgr\Model\Entity\Category', 'store' => 'PwdMgr\Model\Entity\Store');
        foreach ($keys as $key => $class) {
            $this->getLogger()->addInfo('Removing Permissions for '.$class);
            $aclManager->revokeClassPermissions($class, $user);

            $this->getLogger()->addInfo('Searching for Key: "'.$key.'"');
            if (isset($permission[$key])) {
                $builder = new MaskBuilder();
                foreach ($permission[$key] as $perm => $state) {
                    $this->getLogger()->addInfo('Builder set '.$perm);
                    if ($state) {
                        $builder->add($perm);
                    }
                }

                $aclManager->addClassPermission($class, $builder->get(), $user);
            }
        }
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
     * Get Key Service
     * @return KeyService
     */
    protected function getKeyService()
    {
        return $this->serviceKey;
    }

    /**
     * @return Entity
     */
    public function getNew()
    {
        return new Entity();
    }
}
