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

use PwdMgr\Model\Entity\Category;
use PwdMgr\Model\Entity\Key;
use PwdMgr\Model\Entity\User;
use PwdMgr\Service\Exception\UserNotFoundException;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\JsonResponse;
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
 * @PreAuthorize("hasRole('ROLE_ADMIN')")
 * @Route("/store")
 */
class StoreController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/create")
     * @Secure(roles="ROLE_ADMIN")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function createAction(Request $request)
    {
        $object = $this->getService()->getNew();

        return $this->formHandler($object, $request, true);
    }

    /**
     * @param Request $request
     *
     * @Route("/edit/{id}")
     * @Secure(roles="ROLE_ADMIN")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function editAction(Request $request)
    {
        try {
            $object = $this->getService()->find($request->get('id'));

            return $this->formHandler($object, $request);
        } catch (UserNotFoundException $e) {

            return new JsonResponse(sprintf('Row with ID %s not found', $request->get('id')));
        } catch (\Exception $e) {
            $this->getLogger()->addCritical($e->getMessage());
        }
    }

    /**
     * @param Category $object
     * @param Request  $request
     * @param bool     $persist
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    protected function formHandler(Category $object, Request $request, $persist = false)
    {

        if ($persist) {
            $form = $this->createForm('form_category', $object, array('action' => $this->generateUrl('pwdmgr_admin_category_create')));
        } else {
            $form = $this->createForm('form_category', $object, array('action' => $this->generateUrl('pwdmgr_admin_category_edit', array('id' => $object->getId()))));
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($persist) {
                    $this->getService()->persist($object);
                }
                $this->getService()->getEm()->getRepository('Model:Category')->recover();
                $this->getService()->flush();
                $this->flashSuccess('Data successfully saved');

                return $this->redirect('/category');
            } catch (\Exception $e) {
                $this->flashError('Error while saving Data: '.$e->getMessage());
            }
        }

        if ($persist) {
            $title = 'Create ';
        } else {
            $title = 'Edit ';
        }

        return $this->render('PwdMgrAdminBundle:Category:form.html.twig', array(
            'form'  => $form->createView(),
            'title' => $title.'Category',
            'icon'  => 'fa fa-list'
        ));
    }

    /**
     * @param Request $request
     *
     * @Route("/delete/{id}")
     * @Secure(roles="ROLE_ADMIN")
     * @Method({"GET", "DELETE"})
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        try {
            $this->getService()->remove($request->get('id'));
            $this->flashSuccess('Data successfully removed');
        } catch (UserNotFoundException $e) {
            $this->flashError('Object with this ID not found ('.$request->get('id').')');
        } catch (\Exception $e) {
            $this->flashError('Unexpected Error: '.$e->getMessage());
        }

        return $this->redirect('/user/list');
    }

    /**
     * @Route("/list")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     * @Method({"GET"})
     *
     * @return array
     */
    public function listAction()
    {
        return array();
    }

    /**
     * @Route("/")
     * @Template()
     * @Method({"GET"})
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
     * @Secure(roles="ROLE_ADMIN")
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