<?php

namespace Projekt\SklepBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MovieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                        'label' => 'Wpisz tytuł',
                        ))
            ->add('category', null, array(
                        'label' => 'Wybierz kategorie',
                        ))
            ->add('description', 'textarea', array(
                        'label' => 'Podaj opis',
                        ))
            ->add('director', 'text', array(
                        'label' => 'Podaj reżysera',
                        ))
            ->add('price', 'money', array(
                        'label' => 'Podaj cene',
                        ))
            ->add('imageurl', 'url', array(
                        'label' => 'Podaj adres do zdjęcia',
                        'attr' => array('placeholder' => 'Obrazek w wymiarach 300x150 !!!!'),
                        ))
            ->add('stars', 'choice', array(
                     'label' => 'Podaj ilość gwiazdek',
                     'choices' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'),
                    'preferred_choices' => array('3'),
                ))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Projekt\SklepBundle\Entity\Movie'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'projekt_sklepbundle_movie';
    }
}
