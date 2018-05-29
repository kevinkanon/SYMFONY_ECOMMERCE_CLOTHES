<?php
// src/Ecommerce/EcommerceBundle/Controller/ProduitsController.php


namespace Ecommerce\EcommerceBundle\Controller;

use Ecommerce\EcommerceBundle\Entity\Produits;
use Ecommerce\EcommerceBundle\Form\RechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManagerInterface;
use Ecommerce\EcommerceBundle\Entity\Categories;

class ProduitsController extends Controller
{
    public function produitsAction(Request $request, Categories $categorie = null)
    {
        // Page d'accueil qui liste tous les produits

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        if ($categorie != null)
        // Retourne les Produits en fonction des catégories grâce au queryBuilder byCategorie() requête personaliée créee dans le repository et appelée ici
            $findProduits = $em->getRepository('EcommerceBundle:Produits')->byCategorie($categorie);
        else
            $findProduits = $em->getRepository('EcommerceBundle:Produits')->findBy(array('disponible' => 1));
        
        // On retire la possibilité de rajouter un article au panier s'il y est déjà
        if($session->has('panier'))
            $panier = $session->get('panier');
        else 
            $panier = false;

        $produits = $this->container->get('knp_paginator')->paginate(
            $findProduits,
            $request->query->get('page', 1) /*page number commence par défaut à page 1*/,3/*limit per page // nbre élément par page*/);

        return $this->render('EcommerceBundle:Default:produits/layout/produits.html.twig', array('produits' => $produits,
                                                                                                 'panier' => $panier));
    }

    
    public function presentationAction($id, Request $request)
    {
        // presentation d'un seul produit

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository('EcommerceBundle:Produits')->find($id);

        // On retire la possibilité de rajouter un article au panier s'il y est déjà
        if(!$produit)
            throw new NotFoundHttpException("La page n'existe pas");

        if($session->has('panier'))
            $panier = $session->get('panier');
        else 
            $panier = false;
        

        return $this->render('EcommerceBundle:Default:produits/layout/presentation.html.twig', array('produit' => $produit,
                                                                                                 'panier' => $panier));
    }


    public function rechercheAction()
    {
        // simple construction du champs du formulaire / Aucune route à prévoir
        $rechercheType = new RechercheType();
        $form = $this->createForm(RechercheType::class);
        return $this->render('EcommerceBundle:Default:recherche/modulesUsed/recherche.html.twig', array('form' => $form->createView()));
    }


    public function rechercheTraitementAction(Request $request)
    {
        $form = $this->createForm(RechercheType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            //$form['recherche']->getData();
            //Traitement de la requête tapée dans la barre de recherche générée par rechercheAction
            //->recherche() est notre queryBuilder permettant de retourner les articlesen fonction du nom tapé dans a barre de recherche
            $em = $this->getDoctrine()->getManager();
            $produits = $em->getRepository('EcommerceBundle:Produits')->recherche($form['recherche']->getData());
        } else
            {
                throw new NotFoundHttpException('Page inexistante');
            }

        return $this->render('EcommerceBundle:Default:produits/layout/produits.html.twig', array('produits' => $produits));
    }

    
}
