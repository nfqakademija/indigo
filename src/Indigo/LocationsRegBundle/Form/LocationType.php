<?php
/**
 * Created by PhpStorm.
 * User: Tadas
 * Date: 2015-03-22
 * Time: 21:04
 */

namespace Indigo\LocationsRegBundle\Form;

use Indigo\LocationsRegBundle\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active', 'choice', [
                'multiple' => false,
                'expanded' => true,
                'choices'  => [
                    Location::STATE_ACTIVE => 'state.active',
                    Location::STATE_INACTIVE => 'state.inactive',
                ],
            ])
            ->add('title', 'text')
            ->add('address', 'text');

        //Submit button

        $builder->add('save', 'submit', [
            'label' => 'save.button'
        ]);

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Indigo\LocationsRegBundle\Entity\Location'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'location';
    }
}
