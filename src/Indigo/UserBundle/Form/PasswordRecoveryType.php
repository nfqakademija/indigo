<?php

namespace Indigo\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class PasswordRecoveryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('password', 'repeated',[
                'type' => 'password',
                'required' => true,
                'options' => [
                    'attr' => [
                        'class' => 'form-control input-lg'
                    ]
                ],
                'first_options'  => array('label' => 'user.form.password'),
                'second_options' => array('label' => 'user.form.repeat_password')
            ])
            ->add( 'change', 'submit', [
                    'label' => 'user.form.title.change_password',
                    'attr' => [
                        'class' => 'signin-btn bg-primary'
                    ]
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
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'indigo_user_recover_password';
    }
}
