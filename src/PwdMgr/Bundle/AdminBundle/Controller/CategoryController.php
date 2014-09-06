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

use PwdMgr\Service\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Class CategoryController
 *
 * @package PwdMgr\AdminBundle\Controller
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 * @PreAuthorize("hasRole('ROLE_ADMIN')")
 * @Route("/category")
 */
class CategoryController extends Controller
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
     * @Route("/tree")
     * @Template()
     * @Method({"GET"})
     *
     * @return array
     */
    public function treeAction()
    {
        $repo = $this->getService()->getEm()->getRepository('Model:Category');

        $arrayTree = $repo->childrenHierarchy(null, false, array(
            'decorate' => true,
            'representationField' => 'slug',
            'html' => true,
            'rootOpen' => '<ol class="dd-list">',
            'rootClose' => '</ol>',
            'childOpen' => '<li class="dd-item" data-id="%s">',
            'idField'   => 'id',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                return '<div class="dd-handle" data-id="'.$node['id'].'"><a href="/category/edit/'.$node['id'].'" class="editNode">'.$node['name'].'</a></div>';
            }
        ));

        return array('tree' => $arrayTree);
    }

    /**
     * @param Request $request
     *
     * @Route("/update")
     * @Method({"POST"})
     * @return JsonResponse
     */
    public function updateAction(Request $request)
    {
        try {
            $data = json_decode($request->get('data'));
            $service = $this->getService();
            $result = $service->updateTree($data);

            if (is_array($result)) {
                $this->getLogger()->addInfo('Tree warnings', $result);
            }
        } catch (\Exception $e) {
            $this->getLogger()->addError('Uncout Exception', $e);

            return new JsonResponse(array('success' => 0, 'error' => $e->getMessage()));
        }

        return new JsonResponse(array('success' => 1));
    }

    /**
     * @return \PwdMgr\Service\User
     */
    protected function getService()
    {
        return $this->get('service_category');
    }


    /**
     * @param Request $request
     *
     * @Route("/{id}")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     * @Method({"GET"})
     * @return array
     * @todo
     */
    public function detailAction(Request $request)
    {
        return array();
    }

}
