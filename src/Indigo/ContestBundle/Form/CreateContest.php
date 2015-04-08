<?php
/**
 * Created by PhpStorm.
 * User: Tadas
 * Date: 2015-04-02
 * Time: 08:26
 */
namespace Indigo\ContestBundle\Form;

use Indigo\ContestBundle\Entity\Data;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateContest extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function formBuild(FormBuilderInterface $builder, array $options){

        $builder
            ->add('contest_title', 'text', ['label' => false, 'attr' => ['placeholder' => $this->st('create_contest.form.contest_title')]])
            ->add('image', 'file', ['label' => false, 'required' => false])
            ->add('contest_privacy', 'checkbox', ['label' => $this->st('create_contest.form.contest_privacy'), 'required' => false])
            ->add('contest_type', 'choice',
                ['label' => false,
                    'choices' => [0 => $this->st('create_contest.form.contest_type.single'), 1 => $this->st('create_contest.form.contest_type.team')],
                    'placeholder' => $this->st('create_contest.form.contest_type'),
                    'required' => true])
            ->add('table_name', 'text', ['label' => false, 'attr' => ['placeholder' => $this->st('create_contest.form.table_name')]])
            ->add('save', 'submit', ['label'=> 'create_contest.form.submit', 'attr' => ['placeholder' => $this->st('create_contest.form.submit')]]);

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Indigo\ContestBundle\Entity\Data',
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Data';
    }
}