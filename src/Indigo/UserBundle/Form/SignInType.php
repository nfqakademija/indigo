<?php

namespace Indigo\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class SignInType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                'text',
                [
                    'constraints' => [
                        new Email(['checkMX' => true]),
                        new Length(['min' => 3])
                    ]
                ]
            )
            ->add('password', 'password', [
                'constraints' => [
                    new Length(['min' => 3])
                ]
            ])
            ->add('signin', 'submit', [
                    'label' => 'user.signin'
                ]
            );
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
        return 'indigo_userbundle_signin';
    }
}
