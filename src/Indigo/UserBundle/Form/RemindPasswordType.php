<?php

namespace Indigo\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;


class RemindPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('email', 'text')
            ->add( 'remind', 'submit', [
                    'label' => 'user.form.send_password_reset_link'
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
        return 'indigo_user_remind_password';
    }
}
