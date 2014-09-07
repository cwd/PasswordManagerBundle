<?php

namespace PwdMgr\Bundle\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class DefaultController
 *
 * @package PwdMgr\Bundle\AdminBundle\Controller
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 * @PreAuthorize("hasRole('ROLE_USER')")
 *
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Method("GET")
     * @Secure(roles="ROLE_USER")
     * @Template()
     * @return array()
     */
    public function indexAction()
    {
        $category = $this->get('service_category')->find(54);

        return array('name' => 'fff', 'category' => $category, 'user' => $this->getUser());
    }

    /**
     * @Route("/403")
     * @Method("GET")
     *
     * @return RedirectResponse
     */
    public function accessDeniedAction()
    {
        $this->flashError('Security - 403 FORBIDDEN');

        return $this->redirect('/');
    }
}
