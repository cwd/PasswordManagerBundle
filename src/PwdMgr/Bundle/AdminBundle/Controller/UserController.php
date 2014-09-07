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

use Oneup\AclBundle\Security\Authorization\Acl\AclProvider;
use PwdMgr\Model\Entity\User;
use PwdMgr\Service\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class UserController
 *
 * @package PwdMgr\AdminBundle\Controller
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 * @PreAuthorize("hasRole('ROLE_USER')")
 * @Route("/user")
 */
class UserController extends Controller
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
     * @param User    $user
     *
     * @ParamConverter("user", class="Model:User")
     * @Route("/edit/{id}")
     * @Method({"GET", "POST"})
     * @PreAuthorize("hasRole('ROLE_ADMIN') or hasPermission(#user, 'EDIT')")
     * @return array
     */
    public function editAction(Request $request, User $user)
    {
        try {
            return $this->formHandler($user, $request);
        } catch (UserNotFoundException $e) {
            $this->flashError(sprintf('Row with ID %s not found', $request->get('id')));

            if ($this->getContext()->isGranted('ROLE_ADMIN')) {
                return $this->redirect($this->generateUrl('pwdmgr_admin_user_list'));
            }
        } catch (\Exception $e) {
            $this->getLogger()->addCritical($e->getMessage());
        }
    }

    /**
     * @param User    $object
     * @param Request $request
     * @param bool    $persist
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    protected function formHandler(User $object, Request $request, $persist = false)
    {

        $form = $this->createForm('form_user', $object);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($request->getPassword() && trim($request->getPassword()) != '') {
                    $object->setPassword($request->getPassword());
                }

                if ($persist) {
                    $this->getService()->persist($object);
                    $this->getService()->createKeys($object, $request->getPassword());
                }
                $this->getService()->flush();
                $this->flashSuccess('Data successfully saved');

                if ($persist) {
                    $this->getAclManager()->addObjectPermission($object, MaskBuilder::MASK_OPERATOR, $object);
                    $this->getAclManager()->addObjectFieldPermission($object, 'roles', MaskBuilder::MASK_VIEW, $object);

                    return $this->redirect('/auth/updatesecret');
                }

                if ($this->getContext()->isGranted('ROLE_ADMIN')) {
                    return $this->redirect($this->generateUrl('pwdmgr_admin_user_list'));
                }

                return $this->redirect('/');
            } catch (\Exception $e) {
                $this->flashError('Error while saving Data: '.$e->getMessage());
            }

        }

        return $this->render('PwdMgrAdminBundle:Generic:form.html.twig', array(
            'form'  => $form->createView(),
            'title' => 'User',
            'icon'  => 'fa fa-user'
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
     * Set/Edit a Users permission
     *
     * @param Request $request
     * @param User    $user
     *
     * @Route("/permission/{id}")
     * @ParamConverter("user", class="Model:User")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     * @return Response
     */
    public function permissionAction(Request $request, User $user)
    {
        if ($request->isMethod('post')) {
            $this->getService()->setGlobalPermission($user,
                array('category' => $request->get('category'), 'store' => $request->get('store')),
                $this->getAclManager()
            );
            $this->getService()->flush();
        }

        return array('user' => $user);
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
        $this->get('grid_user');

        return array();
    }

    /**
     * Grid action
     *
     * @Route("/grid")
     * @Secure(roles="ROLE_ADMIN")
     * @return Response
     * @Method({"GET"})
     */
    public function gridAction()
    {
        return $this->get('grid_user')->execute();
    }

    /**
     * @Route("/")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     * @Method({"GET"})
     *
     * @return array
     */
    public function indexAction()
    {
        return $this->redirect('/user/list');
    }

    /**
     * @return \PwdMgr\Service\User
     */
    protected function getService()
    {
        return $this->get('service_user');
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
