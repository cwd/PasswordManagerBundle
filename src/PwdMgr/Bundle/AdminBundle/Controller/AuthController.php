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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * AuthController
 *
 * @package PwdMgr\Bundle\AdminBundle\Controller
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 *
 * @Route("/auth")
 */
class AuthController extends Controller
{
    /**
     * Login Action
     *
     * @param Request $request
     *
     * @Route("/login", name="auth_login")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function loginAction(Request $request)
    {
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @param string $passwd
     *
     * @Route("/passwd/{passwd}")
     * @Template()
     * @Method({"GET"})
     * @return array
     */
    public function passwdAction($passwd)
    {
        echo password_hash($passwd, PASSWORD_BCRYPT, array("cost" => 13))."<br />";

        return array();
    }

    /**
     * @Route("/login_check", name="auth_security_check")
     * @Method({"GET"})
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="auth_logout")
     * @Method({"GET"})
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
}