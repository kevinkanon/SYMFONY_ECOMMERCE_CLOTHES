<?php

namespace Utilisateur\UtilisateurBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Spipu\Html2Pdf\Html2Pdf;

class UtilisateurController extends Controller
{
    // $this->container->get('security.token_storage')->getToken()->getUser() === $this->getUser()
    //byFacture() est notre repository qui selectionne une commande validée et son utilisateur associé
    public function facturesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $factures = $em->getRepository('EcommerceBundle:Commandes')->byFacture($this->getUser());
        
        return $this->render('UtilisateurBundle:Default:layout/facture.html.twig', array('factures' => $factures));
    }


    public function facturesPDFAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        // on vérifie que l'utilisateur passé est bien nous
        $facture = $em->getRepository('EcommerceBundle:Commandes')->findOneBy(array('utilisateur' => $this->getUser(),
                                                                                     'valider' => 1,
                                                                                     'id' => $id));
        
        if (!$facture) 
        {
            $this->get('session')->getFlashBag()->add('error', 'Une erreur est survenue');
            return $this->redirectToRoute('facutres');
        }

        // Appel de la fonction facture() du service setNewFacture qui centralise le code d'édition de PDF
        $this->container->get('setNewFacture')->facture($facture);
    }
}

//$this->container->get('setNewReference')->reference()); //Service