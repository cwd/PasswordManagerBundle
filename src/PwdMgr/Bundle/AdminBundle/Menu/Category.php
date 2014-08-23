<?php
/*
 * This file is part of ichliebediemarke.com.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PwdMgr\Bundle\AdminBundle\Menu;

use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use JMS\DiExtraBundle\Annotation as DI;
use PwdMgr\Model\Repository\CategoryRepository;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class Main
 *
 * @DI\Service("service_menu_category")
 *
 * @package PwdMgr\Bundle\AdminBundle\Menu
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 */
class Category extends ContainerAware
{
    /**
     * @param FactoryInterface $factory
     * @param array            $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options = array())
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttributes(array('class' => 'nav nav-main'));

        $data = $this->container->get('em')->getRepository('Model:Category')->childrenHierarchy();
        $this->genMenu($menu, $data, true);

        return $menu;
    }

    /**
     * @param array $parent
     * @param array $nodes
     */
    protected function genMenu($parent, $nodes, $master = false)
    {
        foreach ($nodes as $node) {
            $newParent = $parent->addChild($node['name'], array('uri' => '#'));
            if ($newParent->getLevel() == 1) {
                $newParent->setAttribute('icon', 'fa fa-angle-right');
            }


            if (!$master) {
                $newParent->setChildrenAttributes(array('class' => 'nav nav-children'));
            }


            if (isset($node['__children']) && count($node['__children']) > 0) {
                $newParent->setAttribute('class', 'nav nav-parent');

                $this->genMenu($newParent, $node['__children']);
            } else {
                $newParent->setUri('/store/'.$node['slug']);
            }
        }
    }
}