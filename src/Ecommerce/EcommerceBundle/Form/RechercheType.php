<?php
// src/Ecommerce/EcommerceBundle/Form/rechercheType.php

namespace Ecommerce\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Ici nous allons faire notre formulaire en PHP
        //fini le html
        $builder
            ->add('recherche', TextType::class, 
                    array('label' => 'false',
                          'attr' =>array('class' => 'input-medium search-query'))
            );
    }

    public function getBlockPrefix()
    {
        return 'ecommerce_ecommercebundle_recherche';
    }
    
    /**
     * un formulaire se construit autour d'un objet. Ici, on a indiqué à Symfony quelle était la classe de cet objet 
     * grâce à la méthodeconfigureDefaults(), dans laquelle on a défini l'optiondata_class.
     */
    /*public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Task::class,
        ));
    }*/

     
}