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

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class Main
 *
 * @package PwdMgr\Bundle\AdminBundle\Menu
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 */
class Main extends ContainerAware
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
        $menu->addChild('Dashboard', array('uri' => '/'))
            ->setAttribute('icon', 'fa fa-home');

        return $menu;
    }
}