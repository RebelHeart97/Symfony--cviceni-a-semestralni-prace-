<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class playersFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class)
            ->add('jmeno', Filters\TextFilterType::class)
            ->add('prijmeni', Filters\TextFilterType::class)
            ->add('datNar', Filters\DateFilterType::class,array(
    'years' => range(1920, date('Y'))))
            ->add('zemePuvodu', Filters\TextFilterType::class)
            ->add('vyska', Filters\NumberFilterType::class)
            ->add('vaha', Filters\NumberFilterType::class)
            ->add('drzeniHole', Filters\TextFilterType::class)
            ->add('pozice', Filters\TextFilterType::class)
            ->add('tym', Filters\NumberFilterType::class)
        
        ;
        $builder->setMethod("GET");


    }

    public function getBlockPrefix()
    {
        return null;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
