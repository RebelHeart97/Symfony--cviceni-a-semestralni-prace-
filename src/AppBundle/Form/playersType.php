<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class playersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('jmeno')
            ->add('prijmeni')
            ->add('datNar', DateType::class,array(
    'years' => range(1920, date('Y'))))
            ->add('zemePuvodu')
            ->add('vyska')
            ->add('vaha')
            ->add('drzeniHole')
	          ->add('pozice')
            ->add('tym')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\players'
        ));
    }
}
