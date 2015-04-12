<?php

namespace Indigo\ContestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContestType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', [
                'class' => 'Indigo\\UserBundle\\Entity\\User'
            ])
            ->add('contestTitle', 'text')
            ->add('pathForImage')
            ->add('tableName')
            ->add('contestPrivacy')
            ->add('contestType')
            ->add('contestCreationDate')
            ->add('contestStartingDate')
            ->add('contestEndDate')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Indigo\ContestBundle\Entity\Contest'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'indigo_contestbundle_contest';
    }
}
