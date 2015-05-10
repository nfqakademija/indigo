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
            /*->add('isActive', 'checkbox', [
                'required' => false,
            ])
            ->add('isLocked', 'checkbox', [
                'required' => false,
            ])*/
            ->add('cardId', 'number', [
                'label' => 'user.user_cardid',
                'required' => false,
                'attr' => [
                    'placeholder' => 'user.input_your_cardid'
                ]
            ])
            ->add('username', 'text', [
                'label' => 'user.user_username',
                'required' => false,
                'attr' => [
                    'placeholder' => 'user.input_your_username'
                ]
            ])
            ->add('name', 'text', [
                'label' => 'user.user_name',
                'required' => false,
                'attr' => [
                    'placeholder' => 'user.input_your_name'
                ]
            ])
            /*->add('email', 'email')*/
            ->add('picture', 'file', [
                'label' => 'user.profile_picture',
                'required' => false,
                'attr' => [
                    'class' => 'file profilePicture'
                ]
            ])
            /*->add('registrationDate', 'date', [
                'attr' => [
                    'class' => 'datepicker',
                ],
                'widget' => 'single_text',
                'html5' => false,
            ])*/
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
