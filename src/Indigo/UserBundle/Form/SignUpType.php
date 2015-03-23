<?php

namespace Indigo\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class SignUpType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', ['mapped' => false])
            ->add('email', 'text')
            ->add('password','password')
            ->add(
                'signup',
                'submit',
                [
                    'label' => 'user.signup'
                ]
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Indigo\UserBundle\Entity\User',
                'validation_groups' => ['Default', 'Profile'],
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'indigo_userbundle_up';
    }
}
