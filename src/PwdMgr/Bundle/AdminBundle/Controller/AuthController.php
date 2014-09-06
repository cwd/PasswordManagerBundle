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

use PwdMgr\Service\User;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\Secure;

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
     * Lets user update password of secret key
     *
     * @param Request $request
     *
     * @Secure(roles="ROLE_USER")
     * @Route("/updatesecret")
     * @Method({"GET", "POST"})
     *
     * @return misc
     */
    public function updateSecretPasswordAction(Request $request)
    {
        $form = $this->createForm('form_update_passphrase');
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var User $service */
            $service = $this->get('service_user');
            if ($form->get('oldpassword')->getData() == $form->get('password')->getData()) {
                $form->get('oldpassword')->addError(new FormError('Old and new passwords are not allowed to be the same!'));

            }

            if (!$service->checkKeyPassword($this->getUser(), $form->get('oldpassword')->getData())) {
                $form->get('oldpassword')->addError(new FormError('Password dont unlock key'));
            }

            if ($form->isValid()) {
                try {
                    $service->updatePasshrase($this->getUser(), $form->get('oldpassword')->getData(), $form->get('password')->getData());
                    $service->flush();
                    $this->flashSuccess('Data successfully saved');

                    return $this->redirect('/user/list');

                } catch (\Exception $e) {
                    $this->flashError('Error while saving Data: ' . $e->getMessage());
                }
            }
        }

        return $this->render('PwdMgrAdminBundle:Auth:form.html.twig', array(
            'form'  => $form->createView(),
            'title' => 'Change secret key passhrase',
            'icon'  => 'fa fa-lock'
        ));
    }

    /**
     * Lets user update his profile
     *
     * @param Request $request
     *
     * @Route("/profile")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function profileAction(Request $request)
    {

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
     * @Method({"GET", "POST"})
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