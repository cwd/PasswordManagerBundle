<?php

namespace Acme\DemoBundle\Controller;

use AdmMgr\Model\Entity\Key;
use AdmMgr\Model\Entity\Store;
use AdmMgr\Model\Entity\User;
use Cwd\Bundle\SSLCryptBundle\Services\SSL;
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
         * @var SSL $ssl
         */
        $ssl = $this->get('cwd.bundle.sslcrypt.ssl');
        /**
        $ssl->generateKey('1234');

        $key = new Key();
        $key->setPublic($ssl->getPublicKey())
            ->setPrivate($ssl->getPrivateKey());

        $user = new User();
        $user->setEmail('max@mustermann.at')
             ->setLastname('Mustermann')
             ->setFirstname('Max')
             ->setKey($key);

        $key->setUser($user);

        $this->getDoctrine()->getManager()->persist($key);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();
        **/

        /**
         * @var User $user
         */
        $user1 = $this->getDoctrine()->getManager()->getRepository('Model:User')->find(1);
        $key1 = $user1->getKey()->getPublic();

        $user2 = $this->getDoctrine()->getManager()->getRepository('Model:User')->find(2);
        $key2 = $user2->getKey()->getPublic();

        $publicKey = $user1->getKey()->getPublic();
        $privateKey = $user1->getKey()->getPrivate();

        $publicKey2 = $user2->getKey()->getPublic();
        $privateKey2 = $user2->getKey()->getPrivate();

        echo '<pre>';
        $store = $this->getDoctrine()->getManager()->getRepository('Model:Store')->find(3);
        $data = $store->getData();
        $envKey = json_decode($store->getEnvKey());
        $data = $ssl->decrypt($data, $envKey, $privateKey, '1234');
        print "Decypted data:\t\t$data\n";
        echo '</pre>';

        exit;

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
