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
            'label' => 'creator',
            'class' => 'Indigo\UserBundle\Entity\User',
            'property' => 'username',
            'required' => true
        ])
        ->add('contestTitle', 'text', [
            'label' => 'create_contest.form.contest_title',
            'required' => true
        ])
        ->add('image', 'file', [
            'label' => 'create_contest.form.contest_logo',
            'required' => false,
            'attr' => ['class' => 'file contestLogo'],
        ])
        ->add('tableName', 'text', [
            'label' => 'create_contest.form.table_name',
            'required' => true
        ])
        ->add('contestType', 'choice', [
            'label' => 'create_contest.form.contest_type',
            'expanded' => true,
            'multiple' => false,
            'choices' => [
                1 => 'create_contest.form.contest_type.team',
                0 => 'create_contest.form.contest_type.single'
            ],
            'required' => true
        ])
        ->add('contestStartingDate', 'datetime', [
            'label' => 'create_contest.form.contest_starting_date',
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'attr' => [
                'placeholder' => date('Y-m-d'),
                'class' => 'input-group date'
            ],
            'html5' => false
        ])
        ->add('contestEndDate', 'datetime', [
            'label' => 'create_contest.form.contest_end_date',
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'attr' => [
                'placeholder' => date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days')),
                'class' => 'input-group date'
            ],
            'html5' => false
        ])
        ->add('contestPrivacy', 'checkbox', [
            'label' => false,
            'required' => false,
            'attr' => [
                'class' => 'privacySwitchButton',
                'data-size' => 'normal',
                'data-off-color' => 'default',
                'data-on-color' => 'success',
                'data-handle-width' => 50,
            ],
        ]);
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
