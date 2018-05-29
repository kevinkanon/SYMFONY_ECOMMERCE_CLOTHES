<?php

namespace Ecommerce\EcommerceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Ecommerce\EcommerceBundle\Form\MediaType;

class ProduitsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', TextType::class)
                ->add('description', TextType::class)
                ->add('prix', NumberType::class)
                ->add('disponible')
                ->add('image', MediaType::class) // IMBRICATION DE FORMULAIRE
                /*->add('categorie') soit on ajoute public function __toString(){return $this->getNom();} ds l'entité Categorie ou
                soit méthode en dessous*/
               /*->add('tva') soit on ajoute public function __toString(){return $this->getNom();} ds l'entité Tva ou
                soit méthode en dessous*/
                ->add('categorie', EntityType::class, array(
                    // query choices from this entity
                    'class' => 'EcommerceBundle:Categories',
                    // use the User.username property as the visible option string
                    'choice_label' => 'nom',
                    // used to render a select box, check boxes or radios
                    //'multiple' => true,
                    //'expanded' => true,
                )) 
                ->add('tva', EntityType::class, array(
                      'class' => 'EcommerceBundle:Tva',
                      'choice_label' => 'nom',));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ecommerce\EcommerceBundle\Entity\Produits'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ecommerce_ecommercebundle_produits';
    }


}
