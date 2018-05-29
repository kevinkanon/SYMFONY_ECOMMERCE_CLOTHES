<?php 

namespace Ecommerce\EcommerceBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\EntityManager;

class GetReference 
{
   
    protected $em;
    protected $securityContext;

    public function __construct(TokenStorage $securityContext, EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->securityContext = $securityContext;
    }


    /**
     * récupère une seule commande validée 
     * s'il n'ya pas encore de facture la reference est forcément 1 
     * sinon si ce n'est le 1er élément, on récupère la dernière reférence de la dernière commande et on l'incrémente
     * 
     * @param GetResponseEvent $event
     * @return void
     */
    public function reference()
    {
        $reference = $this->em->getRepository('EcommerceBundle:Commandes')
                              ->findOneBy(array('valider' => 1),
                                          array('id' => 'DESC'),1,1);

        if (!$reference)
            return 1;
        else
            return $reference->getReference() + 1;
    }
    
}