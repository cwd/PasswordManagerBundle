<?php
/*
 * This file is part of password-manager.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AdmMgr\Bundle\AdminBundle\Controller;

use AdmMgr\Model\Entity\Key;
use AdmMgr\Model\Entity\User;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Class UserController
 *
 * @package AdmMgr\AdminBundle\Controller
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 * @PreAuthorize("hasRole('ROLE_ADMIN')")
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/create")
     * @Secure(roles="ROLE_ADMIN")
     * @return array
     */
    public function createAction(Request $request)
    {
        $object = $this->getService()->getNew();

        return $this->_formHandler($object, $request, true);
    }

    /**
     * @param Request $request
     *
     * @Route("/edit/{id}")
     * @Secure(roles="ROLE_ADMIN")
     * @return array
     */
    public function editAction(Request $request)
    {
        try {
            $asset = $this->getService()->find($request->get('id'));

            return $this->_formHandler($asset, $request);
        } catch (UserNotFoundException $e) {
            $this->flashError(sprintf('Row with ID %s not found', $request->get('id')));

            return $this->redirect('/user/list');
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
    protected function _formHandler(User $object, Request $request, $persist = false)
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
                    /**
                     * @TODO This should go in a service class
                     * @var SSL $ssl
                     */
                    $ssl = $this->get('cwd.bundle.sslcrypt.ssl');
                    $ssl->generateKey($request->getPassword());
                    $key = new Key();
                    $key->setPublic($ssl->getPublicKey())
                        ->setPrivate($ssl->getPrivateKey())
                        ->setUser($object);
                    $this->getService()->persist($key);
                }
                $this->getService()->getEm()->flush();
                $this->flashSuccess('Data successfully saved');

                return $this->redirect('/user/list');
            } catch (\Exception $e) {
                $this->flashError('Error while saving Data: '.$e->getMessage());
            }

        }

        return $this->render('AdmMgrAdminBundle:Generic:form.html.twig', array(
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
     *
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
     * @return Response
     */
    public function gridAction()
    {
        return $this->get('grid_user')->execute();
    }

    /**
     * @Route("/")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        return $this->redirect('/user/list');
    }

    /**
     * @return \AdmMgr\Service\asset
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
     * @return array
     * @todo
     */
    public function detailAction(Request $request)
    {
        return array();
    }

}