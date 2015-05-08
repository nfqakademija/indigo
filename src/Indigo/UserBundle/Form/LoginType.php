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
                    'placeholder' => 'user.form.enter_username_or_email',
                ]
            ])
            ->add('password','password',[
                'attr' => [
                    'placeholder' => 'user.form.enter_password',
                ]
            ])
            ->add(
                'signin',
                'submit',
                [
                    'label' => 'user.form.login'
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
