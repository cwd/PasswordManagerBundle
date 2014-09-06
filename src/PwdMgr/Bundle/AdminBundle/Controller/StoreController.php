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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Class StoreController
 *
 * @package PwdMgr\AdminBundle\Controller
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 * @PreAuthorize("hasRole('ROLE_USER')")
 * @Route("/store")
 */
class StoreController extends Controller
{

    /**
     * @Route("/")
     * @Template()
     * @Method({"GET"})
     * @Secure(roles="ROLE_USER")
     *
     * @return array
     */
    public function indexAction()
    {
        return array();
    }


    /**
     * @param Request $request
     *
     * @Route("/{slug}")
     * @Template()
     * @Secure(roles="ROLE_USER")
     * @Method({"GET"})
     * @return array
     */
    public function detailAction(Request $request)
    {
        $slug = $request->get('slug');
        $category = $this->getService()->findBySlug($slug);

        return array('category' => $category, 'keys' => $category->getStores());
    }

    /**
     * @return \PwdMgr\Service\User
     */
    protected function getService()
    {
        return $this->get('service_category');
    }
}
