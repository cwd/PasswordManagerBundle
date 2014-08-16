<?php

namespace Acme\DemoBundle\Controller;

use Acme\DemoBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WelcomeController extends Controller
{
    public function indexAction()
    {
        /*
         * The action's view can be rendered using render() method
         * or @Template annotation as demonstrated in DemoController.
         */
        #$this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');
        #$this->get('session')->getFlashBag()->add('success', 'Your changes were saved!');
        #$this->get('session')->getFlashBag()->add('error', 'Your changes were saved!');
        #$this->get('session')->getFlashBag()->add('info', 'Your changes were saved!');

        /**
        $row = new User();
        $row->setFirstname('Max')
            ->setLastname('Mustermann')
            ->setEmail('max@mustermann.at')
            ->setCreated(new \DateTime());

        $this->getDoctrine()->getManager()->persist($row);
        $this->getDoctrine()->getManager()->flush();
        **/

        return $this->render('AcmeDemoBundle:Welcome:index.html.twig');
    }

    /**
     * @Route("/list")
     * @Template()
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
     * @param Request $request
     *
     * @Route("/create")
     * @return array
     */
    public function createAction(Request $request)
    {
        $object = new User();

        return $this->_formHandler($object, $request, true);
    }

    /**
     * @param Request $request
     *
     * @Route("/edit/{id}")
     * @return array
     */
    public function editAction(Request $request)
    {
        try {
            $asset = $this->getService()->find($request->get('id'));

            return $this->_formHandler($asset, $request);
        } catch (AssetNotFoundException $e) {
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
                if ($persist) {
                    $this->getDoctrine()->getManager()->persist($object);
                }
                $this->getDoctrine()->getManager()->flush();
                $this->flashSuccess('Data successfully saved');

                return $this->redirect('/');
            } catch (\Exception $e) {
                $this->flashError('Error while saving Data: '.$e->getMessage());
            }

        }

        return $this->render('AcmeDemoBundle:Generic:form.html.twig', array(
            'form'  => $form->createView(),
            'title' => 'User',
            'icon'  => 'fa fa-user'
        ));
    }

    /**
     * @var Logger
     */
    protected $logger = null;

    /**
     * @return SecurityContext
     */
    public function getContext()
    {
        return $this->container->get('security.context');
    }

    /**
     * @return Logger
     */
    public function getLogger()
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
