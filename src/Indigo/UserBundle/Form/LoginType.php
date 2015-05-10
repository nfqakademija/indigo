<?php

namespace Indigo\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Translation;

class LoginType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username','text', [
                'attr' => [
                    'placeholder' => 'user.your_mail',
                ]
            ])
            ->add('password','password',[
                'attr' => [
                    'placeholder' => 'user.password',
                ]
            ])
            ->add(
                'signin',
                'submit',
                [
                    'label' => 'user.sign_in'
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
                'data_class' => null,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'indigo_user_login';
    }
}
