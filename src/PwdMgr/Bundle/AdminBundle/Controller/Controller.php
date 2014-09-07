<?php
/*
 * This file is part of password-manager.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PwdMgr\Bundle\AdminBundle\Controller;

use Monolog\Logger;
use Oneup\AclBundle\Security\Acl\Manager\AclManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class DefaultController
 *
 * @package PwdMgr\AdminBundle\Controller
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 */
class Controller extends SymfonyController
{

    /**
     * @var Logger
     */
    protected $logger = null;

    /**
     * @return AclManager
     */
    protected function getAclManager()
    {
        if (!$this->container->has('oneup_acl.manager')) {
            throw new ServiceNotFoundException('OneUp ACL Manager not set');
        }
        return $this->container->get('oneup_acl.manager');
    }

    /**
     * @return SecurityContext
     */
    protected function getContext()
    {
        return $this->container->get('security.context');
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        if ($this->logger === null) {
            $this->logger = $this->get('logger');
        }

        return $this->logger;
    }

    /**
     * Session Flashmessenger
     *
     * @param string $type
     * @param string $message
     */
    protected function flash($type = 'info', $message = null)
    {
        $this->get('session')->getFlashBag()->add(
            $type,
            $message
        );
    }

    /**
     * @param string $message
     */
    protected function flashInfo($message)
    {
        $this->flash('info', $message);
    }

    /**
     * @param string $message
     */
    protected function flashSuccess($message)
    {
        $this->flash('success', $message);
    }

    /**
     * @param string $message
     */
    protected function flashError($message)
    {
        $this->flash('error', $message);
    }
}
