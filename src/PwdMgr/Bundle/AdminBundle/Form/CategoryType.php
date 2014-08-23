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
 * Class Category Form
 *
 * @package PwdMgr\AdminBundle\Forms
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 *
 * @DI\Service("form_category")
 * @DI\Tag("form.type")
 */
class CategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('parent', 'entity', [
                    'class' => 'Model:Category',
                    'property' => 'name',
                    'label' => 'Parent Node',
                    'empty_value' => 'Choose an option',
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
                return array('default');
            },
            'data_class' => 'PwdMgr\Model\Entity\Category',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'form_category';
    }
}