<?php
/*
 * This file is part of the Password Manager Project.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PwdMgr\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class Update Passhrase
 *
 * @package PwdMgr\AdminBundle\Forms
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 *
 * @DI\Service("form_update_passphrase")
 * @DI\Tag("form.type")
 */
class UpdatePassphraseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('oldpassword', 'password', ['label' => 'Old Password'])
            ->add('password', 'repeated', [
                    'type'  => 'password',
                    'label' => 'New Password',
                    'invalid_message' => 'Password fields must match',
                    'required' => true,
                    'first_options' => ['label' => 'New Password'],
                    'second_options' => ['label' => 'New Password Repeat']
                ]
            )
            ->add('save', 'submit', ['label' => 'Save']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'form_update_passphrase';
    }
}
