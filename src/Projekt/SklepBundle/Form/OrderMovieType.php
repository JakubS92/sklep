<?php

namespace Projekt\SklepBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderMovieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('orderedAt')
            ->add('user', null, array( 'attr'=>array('style'=>'display:none;'), 'label_attr' => array('style'=>'display:none;'))) 
            ->add('movies',  null, array( 'attr'=>array('style'=>'display:none;'), 'label_attr' => array('style'=>'display:none;'))) 
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Projekt\SklepBundle\Entity\OrderMovie'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'projekt_sklepbundle_ordermovie';
    }
}
