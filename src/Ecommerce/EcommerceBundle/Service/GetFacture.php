<?php 

namespace Ecommerce\EcommerceBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
// Bundle pour PDF
use Spipu\Html2Pdf\Html2Pdf;

class GetFacture
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    /**
     * Centralise et gère l'édition de facture sous format PDF pour éviter dupliquer le code dans les controllers
     *
     * @param [type] $facture
     */
    public function facture($facture)
    {
        $html = $this->container->get('templating')->render('UtilisateurBundle:Default:layout/facturePDF.html.twig', array('facture' => $facture));  

        // on récupère le service app.html2pdf
        // on lui passe en arguments les parametres définit dans le constructeur du service
        $html2pdf = $html2pdf = new Html2Pdf('P','A4','fr','true','UTF-8');
        $html2pdf->pdf->SetAuthor('Kevin_KANON');
        $html2pdf->pdf->SetTitle('Facture '.$facture->getReference());
        $html2pdf->pdf->SetSubject('Facture Kevin_KANON');
        $html2pdf->pdf->SetKeywords('facture,Kevin_KANON');
        //SetDisplayMode definit la manière dont le PDF va être affiché par l'utilisateur
        // fullpage: affiche la page entière
        //fullwidth: affiche la largeur maximale de la fenêtre
        // real: utilise la taille réelle
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output('Facture.pdf');
        
        $response = new Response();
        $response->headers->set('Content-type' , 'application/pdf');
        
        return $response;   
    }
    
}