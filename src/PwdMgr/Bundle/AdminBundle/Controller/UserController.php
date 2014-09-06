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

use PwdMgr\Model\Entity\Key;
use PwdMgr\Model\Entity\User;
use PwdMgr\Service\Exception\UserNotFoundException;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\Secure;

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
     *
     * @Route("/edit/{id}")
     * @Method({"GET", "POST"})
     * @PreAuthorize("hasRole('ROLE_ADMIN') or user.getStringId() == #request.get('id')")
     * @return array
     */
    public function editAction(Request $request)
    {
        try {
            $user = $this->getService()->find($request->get('id'));

            return $this->formHandler($user, $request);
        } catch (UserNotFoundException $e) {
            $this->flashError(sprintf('Row with ID %s not found', $request->get('id')));

            if ($this->getContext()->isGranted('ROLE_ADMIN')) {
                return $this->redirect($this->generateUrl('pwdmgr_admin_user_list'));
            }

            return $this->redirect($this->generateUrl('/'));
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