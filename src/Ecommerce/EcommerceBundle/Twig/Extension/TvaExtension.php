<?php
// src/Ecommerce/EcommerceBundle/Twig/Extension/TvaExtension.php

namespace Ecommerce\EcommerceBundle\Twig\Extension;

class TvaExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('tva' , array($this, 'calculTva'))
        );
    }

    public function calculTva($prixHT, $tva)
    {
        return round($prixHT / $tva, 2);
    }

    /*When your extension needs to be compatible with Twig versions before 1.26
    public function getName()
    {
        return 'tva_extension';
    }*/
}