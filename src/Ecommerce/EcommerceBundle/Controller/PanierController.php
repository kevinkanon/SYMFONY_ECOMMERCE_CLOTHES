<?php
// src/Ecommerce/EcommerceBundle/Controller/PanierController.php

namespace Ecommerce\EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ecommerce\EcommerceBundle\Form\UtilisateursAdressesType;
use Ecommerce\EcommerceBundle\Entity\UtilisateursAdresses;
use Ecommerce\EcommerceBundle\Entity\Commandes;
use Ecommerce\EcommerceBundle\Entity\Produits;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;


class PanierController extends Controller
{
    public function menuAction(Request $request)
    {
        // render controller dans la page d'accueil produit.html.twig et page presentation produit afin d'avoir le nomnre d'articles contenu dans le panier
        $session = $request->getSession();
        if(!$session->has('panier'))
            $articles = 0;
        else 
            $articles = count($session->get('panier'));
        
        return $this->render('EcommerceBundle:Default:panier/modulesUsed/panier.html.twig', array('articles' => $articles));

    }
    
    public function ajouterAction($id, Request $request)
    {
        // on récupère la session
        $session = $request->getSession();

        // si la session n'existe pas on l'initialise avec un tableau vide
        if (!$session->has('panier'))
        {
            $session->set('panier',array());
        }
        // ensuite on récupère après l'initialisation pour la manipuler
        $panier = $session->get('panier');

        // $panier[ID DU PRODUIT] => QUANTITE || 
        //1- on verifie que $id est présent dans notre array panier // si la quantité définie n'est pas nul => notre produit $panier[$id] = la nouvelle qté modifiée
        //2- sinon si le produit nexiste pas dans le panier, si $id n'existe pas dans $panier et que la qté n'est pas nulle// notre produit $panier[$id] = la nouvelle qté modifiée
        //3 - sinon par défaut la quantité = 1
        if(array_key_exists($id, $panier))
        {
            //qte = name du <select> dans notre vue presention.html.twig
            if($request->query->get('qte') != null)
            {
                $panier[$id] = $request->query->get('qte');
                $session->getFlashBag()->add('success', 'Quantité modifiée avec succès');
            } 
        } else
            {
                if($request->query->get('qte') != null)
                {
                    $panier[$id] = $request->query->get('qte');
   
                } else 
                    {
                        $panier[$id] = 1;
                    }
                $session->getFlashBag()->add('success', 'Article ajouté avec succès');
            } 
                
        // on met ensuite à jour notre variable panier dans la session panier
        $session->set('panier', $panier);

        return $this->redirectToRoute('panier');
    }

    public function supprimerAction($id, Request $request)
    {
        $session = $request->getSession();
        $panier = $session->get('panier');

        if(array_key_exists($id, $panier))
        {
            unset($panier[$id]);
            //on met ensuite a jour le panier
            $session->set('panier', $panier);
            $session->getFlashBag()->add('success', 'Article supprimé avec succès');
        }

        return $this->redirectToRoute('panier');         
    }

    public function panierAction(Request $request)
    {
        //$session->remove('panier');
        $session = $request->getSession();
        if (!$session->has('panier'))
        {
            $session->set('panier', array());
        }
        // findAray() function perso repository qui selectionne uniquement les produits qui existent dans la session par leur id
        $em = $this->getDoctrine()->getManager();    
        $produits = $em->getRepository('EcommerceBundle:Produits')->findArray(array_keys($session->get('panier')));    
      
        //Ajoute article au panier et dirige sur la page qui liste les articles dans le panier
        return $this->render('EcommerceBundle:Default:panier/layout/panier.html.twig', array('produits' => $produits,
                                                                                             'panier' => $session->get('panier')));
    }


    public function adresseSuppressionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:UtilisateursAdresses')->find($id);   

        // on verifie que c'est le bon utilisater pour permettre la suppression
        if ($this->container->get('security.token_storage')->getToken()->getUser() != $entity->getUtilisateur() || !$entity)
        {
            return $this->redirectToRoute('livraison');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirectToRoute('livraison');
    }

    public function livraisonAction(Request $request)
    {
        // dirige vers la page d'ajout de l'adresse de livraison apres validation du panier 
        $utilisateur = $this->container->get('security.token_storage')->getToken()->getUser();
        $entity = new UtilisateursAdresses();
        $form = $this->createForm(UtilisateursAdressesType::class, $entity);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            $entity->setUtilisateur($utilisateur);
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('livraison');
        }

        return $this->render('EcommerceBundle:Default:panier/layout/livraison.html.twig',  array('utilisateur' => $utilisateur,
                                                                                                 'form' => $form->createView()));
    }

    public function setLivraisonSession(Request $request)
    {
        // verifie les adresses livraison et facturation
        $session = $request->getSession();

        if(!$session->has('adresse'))
            $session->set('adresse', array());
        

        $adresse = $session->get('adresse');

        if($request->request->get('livraison') != null && $request->request->get('facturation') != null)
        {
            $adresse['livraison'] = $request->request->get('livraison');
            $adresse['facturation'] = $request->request->get('facturation');
        } else 
            {
                return $this->redirectToRoute('validation');
            }
        
        $session->set('adresse', $adresse);

        return $this->redirectToRoute('validation');
    }

    public function validationAction(Request $request)
    {
        //dirige vers la page de recapitulatif avant paiement apres validation de l'adresse de livraison
        // execute la methode au dessus
        if($request->isMethod('POST')) 
            $this->setLivraisonSession($request);
        
        $em = $this->getDoctrine()->getManager();
        // forward execute durectement la methode prepareCommande de CommandeController comme un service // retourne un id
        $prepareCommande = $this->forward('EcommerceBundle:Commandes:prepareCommande');
        $commande = $em->getRepository('EcommerceBundle:Commandes')->find($prepareCommande->getContent());

        return $this->render('EcommerceBundle:Default:panier/layout/validation.html.twig', array('commande' => $commande));
    }
}
