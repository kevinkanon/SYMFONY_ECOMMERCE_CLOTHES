<?php
// src/Ecommerce/EcommerceBundle/Controller/CommandesController.php

// Gere les produits en sessions après avoir cliquer sur PAYER

namespace Ecommerce\EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ecommerce\EcommerceBundle\Entity\UtilisateursAdresses;
use Ecommerce\EcommerceBundle\Entity\Commandes;
use Ecommerce\EcommerceBundle\Entity\Produits;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;


class CommandesController extends Controller
{
    //GESTION COMMANDES
   public function facture(Request $request)
   {
       $em = $this->getDoctrine()->getManager();
       // genere chaine aleatoire ki définit un token car absolument besoin d'un token pr nimporte quel API
       $random = random_int(1, 20);
       //$random  = $this->container->get('security.secure_random');
       $session = $request->getSession();
       $adresse = $session->get('adresse');
       $panier = $session->get('panier');
       $commande = array();
       $totalHT = 0;
       $totalTVA = 0;
 
       $facturation = $em->getRepository('EcommerceBundle:UtilisateursAdresses')->find($adresse['facturation']);
       $livraison = $em->getRepository('EcommerceBundle:UtilisateursAdresses')->find($adresse['livraison']);
       $produits = $em->getRepository('EcommerceBundle:Produits')->findArray(array_keys($session->get('panier')));
 
       foreach ($produits as $produit) 
       {
            $prixHT = ($produit->getPrix() * $panier[$produit->getId()]);
            $prixTTC = ($produit->getPrix() * $panier[$produit->getId()] / $produit->getTva()->getMultiplicate());
            $totalHT += $prixHT;

            // on cree un tableau tva ds lequel on stock le prix de la tva
            if (!isset($commande['tva']['%'.$produit->getTva()->getValeur()]))
                $commande['tva']['%'.$produit->getTva()->getValeur()] = round($prixTTC - $prixHT,2);
                          
            else
                $commande['tva']['%'.$produit->getTva()->getValeur()] += round($prixTTC - $prixHT,2);

            $totalTVA += round($prixTTC - $prixHT,2);
            
            $commande['produit'][$produit->getId()] = array('reference' => $produit->getNom(),
                                                        'quantite' => $panier[$produit->getId()],
                                                        'prixHT' => round($produit->getPrix(),2),
                                                        'prixTTC' => round($produit->getPrix() / $produit->getTva()->getMultiplicate(),2));

       }
 
        $commande['livraison'] = array('prenom' => $livraison->getPrenom(),
                                    'nom' => $livraison->getNom(),
                                    'telephone' => $livraison->getTelephone(),
                                    'adresse' => $livraison->getAdresse(),
                                    'cp' => $livraison->getCp(),
                                    'ville' => $livraison->getVille(),
                                    'pays' => $livraison->getPays(),
                                    'complement' => $livraison->getComplement());
        $commande['facturation'] = array('prenom' => $facturation->getPrenom(),
                                    'nom' => $facturation->getNom(),
                                    'telephone' => $facturation->getTelephone(),
                                    'adresse' => $facturation->getAdresse(),
                                    'cp' => $facturation->getCp(),
                                    'ville' => $facturation->getVille(),
                                    'pays' => $facturation->getPays(),
                                    'complement' => $facturation->getComplement());
        $commande['prixHT'] = round($totalHT,2);
        $commande['prixTTC'] = round($totalHT + $totalTVA,2);
        $commande['token'] = bin2hex($random);
        

        return $commande;
   }

    public function prepareCommandeAction(Request $request)
    {
        // pré-enregistre en database les produits choisis avant de cliquer sur payer
        // pré-enregistré mais pas enregistré car le paiement esst pas encore validé
        //mise en session de la commande pr pas avoir à l'enregistrer 2 fois en database au cas ou le client change de page
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        //Si la session n'existe on crée une nouvelle commande ou prend en compte la commande actuelle si elle a  été modifiée
        //sachant qu'elle n'est pas validée setValider(0); setReference(0) avant de la stocker en database
        if(!$session->has('commande'))
            $commande = new Commandes();
        else
            $commande = $em->getRepository('EcommerceBundle:Commandes')->find($session->get('commande'));
         
        $commande->setDate(new \Datetime);
        $commande->setUtilisateur($this->container->get('security.token_storage')->getToken()->getUser());
        $commande->setValider(0);
        $commande->setReference(0);
        $commande->setCommande($this->facture($request));

        if(!$session->has('commande'))
        {
            $em->persist($commande);
            $session->set('commande', $commande);
        }
            
        $em->flush();

        //return response avant d'être utilisée par validationCommandeAction de PanierCOntroller
        return new Response($commande->getId());
    }

    
    /*
     * Cette methode remplace l'api banque.
     */
    public function validationCommandeAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('EcommerceBundle:Commandes')->find($id);
        
        if (!$commande || $commande->getValider() == 1)
            throw new NotFoundHttpException('La commande n\'existe pas');
        
        $commande->setValider(1);
        $commande->setReference($this->container->get('setNewReference')->reference()); //Service
        $em->flush();   
        
        // on supprime toute les sessions à la validation de la commande
        $session = $request->getSession();
        $session->remove('adresse');
        $session->remove('panier');
        $session->remove('commande');
        
        $session->getFlashBag()->add('success','Votre commande est validé avec succès');
        return $this->redirectToRoute('factures');
    }
}
