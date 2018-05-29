<?php
// src/Ecommerce/EcommerceBundle/Form/TestType.php

namespace Ecommerce\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;






class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Ici nous allons faire notre formulaire en PHP
        //fini le html
        $builder
            ->add('email', EmailType::class, array('required' => false))
            ->add('nom', DateType::class)
            ->add('prenom', DateTimeType::class)
            ->add('sexe', ChoiceType::class, array('choices' => array('Homme' => '0',
                                                                      'Femme' => '4'), 'expanded' => true))
            ->add('contenu', TextareaType::class)
            ->add('pays', CountryType::class)
            ->add('date')
            ->add('utilisateur', EntityType::class, array(
                'class'         => 'UtilisateurBundle:Utilisateur',
                'choice_label'  => 'username',
                'multiple'      => false,
                'expanded'      => false
            ))
            ->add('envoyer', SubmitType::class)
        ;
    }

    /*public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Task::class,
        ));
    }*/
}