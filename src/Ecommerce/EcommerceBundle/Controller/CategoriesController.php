<?php
// src/Ecommerce/EcommerceBundle/Controller/CategoriesController.php

namespace Ecommerce\EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoriesController extends Controller
{
    public function menuAction()
    {
        //Inclusion de controller# différent de l'inculusion classique
        //menu.html.twig sera inclus dans le layout general base.html.twig afin d'être visible sur toutes les pages qui extend le layout general
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('EcommerceBundle:Categories')->findAll();

        return $this->render('EcommerceBundle:Default:categories/modulesUsed/menu.html.twig', array('categories' => $categories));
    }

    
}
