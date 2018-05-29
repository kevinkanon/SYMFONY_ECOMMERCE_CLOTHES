<?php
// src/Ecommerce/EcommerceBundle/Controller/ProduitsAdminController.php

namespace Utilisateur\UtilisateurBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Produit controller.
 *
 */
class UtilisateursAdminController extends Controller
{
    /**
     * Liste tous les utilisateurs
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $utilisateurs = $em->getRepository('UtilisateurBundle:Utilisateur')->findAll();

        return $this->render('UtilisateurBundle:Administration:Utilisateurs/layout/index.html.twig', array('utilisateurs' => $utilisateurs));
    }
    /**
     * Liste l'intégralité des adresses et factures du client.
     *
     * @param [type] $id
     * @param Request $request
     * @return void
     */
    public function utilisateurAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $utilisateur = $em->getRepository('UtilisateurBundle:Utilisateur')->find($id);
        $route = $request->attributes->get('_route');
        
        if ($route == 'adminAdressesUtilisateurs')
            return $this->render('UtilisateurBundle:Administration:Utilisateurs/layout/adresses.html.twig', array('utilisateur' => $utilisateur));
        else if ($route == 'adminFacturesUtilisateurs')
            return $this->render('UtilisateurBundle:Administration:Utilisateurs/layout/factures.html.twig', array('utilisateur' => $utilisateur));
        else 
            throw $this->createNotFoundException('La vue n\'existe pas.');
    }
    
}