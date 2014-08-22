<?php
/*
 * This file is part of the WienHolding IT Assets Tool.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PwdMgr\Bundle\AdminBundle\Grid;

use Ali\DatatableBundle\Util\Datatable;
use Cwd\GenericBundle\Grid\Grid;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class Assetgroup
 *
 * @package PwdMgr\Bundle\AdminBundle\Grid
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 *
 * @DI\Service("grid_user")
 */
class User extends Grid
{
    /**
     * @param Datatable $datatable
     *
     * @DI\InjectParams({
     *  "datatable" = @DI\Inject("datatable", strict = false)
     * })
     */
    public function __construct(Datatable $datatable)
    {
        $this->setDatatable($datatable);

        return $this->get();
    }

    /**
     * @return Datatable
     */
    public function get()
    {
        $instance = $this;

        return $this->getDatatable()
            ->setEntity('Model:User', 'x')
            ->setFields(
                [
                    'ID' => 'x.id as xid',
                    'Firstname' => 'x.firstname',
                    'Lastname' => 'x.lastname',
                    'Email' => 'x.email',
                    'Created' => 'x.createdAt',
                    '_identifier_'  => 'x.id',
                ]
            )
            ->setOrder('x.id', 'desc')
            ->setRenderers(
                [
                    5 => [
                        'view' => 'CwdAdminPortoBundle:Grid:_actions.html.twig',
                        'params' => [
                            'edit_route'     => 'pwdmgr_admin_user_edit',
                            //'delete_route'   => 'acme_demo_welcome_delete',
                            //'undelete_route' => 'itasset_admin_user_undelete',
                        ],
                    ],
                ]
            )
            ->setRenderer(
                function (&$data) use ($instance)
                {
                    foreach ($data as $key => $value) {
                        if ($key == 4) {
                            if (null !== $value && $value instanceof \DateTime) {
                                $data[$key] = $value->format('d.m.Y H:i:s');
                            } else {
                                $data[$key] = null;
                            }
                        }
                    }
                }
            )
            ->setHasAction(true)
            ->setSearch(true);
    }
} 