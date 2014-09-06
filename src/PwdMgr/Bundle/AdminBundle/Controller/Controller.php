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
use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use JMS\DiExtraBundle\Annotation as DI;
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
