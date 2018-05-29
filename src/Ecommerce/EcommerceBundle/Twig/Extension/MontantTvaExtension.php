<?php
// src/Ecommerce/EcommerceBundle/Twig/Extension/montantTvaExtension.php

namespace Ecommerce\EcommerceBundle\Twig\Extension;

class MontantTvaExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('montantTva' , array($this, 'montantTva'))
        );
    }

    public function montantTva($prixHT, $tva)
    {
        return round((($prixHT / $tva) - $prixHT), 2);
    }

    /*When your extension needs to be compatible with Twig versions before 1.26
    public function getName()
    {
        return 'montant_tva_extension';
    }*/
}