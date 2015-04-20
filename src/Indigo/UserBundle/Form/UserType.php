<?php

namespace Indigo\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isActive', 'checkbox', [
                'required' => false,
            ])
            ->add('isLocked', 'checkbox', [
                'required' => false,
            ])
            ->add('username')
            ->add('email', 'email')
            ->add('picture', 'file', [
                'required' => false,
            ])
            ->add('registrationDate', 'date', [
                'attr' => [
                    'class' => 'datepicker',
                ],
                'widget' => 'single_text',
                'html5' => false,
            ])
//            ->add('roles')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Indigo\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'indigo_userbundle_user';
    }
}
