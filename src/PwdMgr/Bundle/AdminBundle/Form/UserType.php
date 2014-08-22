<?php
/*
 * This file is part of the Password Manager Project.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PwdMgr\Bundle\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class User Form
 *
 * @package PwdMgr\AdminBundle\Forms
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 *
 * @DI\Service("form_user")
 * @DI\Tag("form.type")
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text')
            ->add('lastname', 'text')
            ->add('email', 'email', ['label' => 'EMail'])
            ->add('password', 'repeated', [
                    'type'  => 'password',
                    'label' => 'Password',
                    'invalid_message' => 'Password fields must match',
                    'required' => true,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repeat Password']
                ]
            )
            ->add('rolesCollection', 'entity', [
                    'class' => 'Model:Role',
                    'property' => 'name',
                    'label' => 'Roles',
                    'multiple' => true,
                    'attr' => ['data-plugin-selectTwo' => '']
                ]
            )
            ->add('save', 'submit', ['label' => 'Save']);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getData();
                if ($data->getId() > 0) {
                    return array('default');
                }

                return array('default', 'password');
            },
            'data_class' => 'PwdMgr\Model\Entity\User',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'form_user';
    }
}