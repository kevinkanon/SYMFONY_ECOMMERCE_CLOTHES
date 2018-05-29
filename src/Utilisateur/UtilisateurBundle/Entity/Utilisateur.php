<?php
// src/Utilisateur/UtilisateurBundle/Entity/Utilisateur.php

namespace Utilisateur\UtilisateurBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Utilisateur
 * 
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="Utilisateur\UtilisateurBundle\Repository\UtilisateurRepository")
 */
class Utilisateur extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * --BIDIRECTIONNELLE-- un utilisateur peut avoir plusieurs commandes
     * * Création ici d'une relation Bi-directionelle, pour récuperer l'entité propriétaire Commandes de Utilisateur. on rajoute aussi donc l'entité propriétaire
     * dans l'entité inverse l'inverse de ManyToOne dans Commande(propriétaire) est ici OneToMany
     * Le mappedBy correspond, lui, à l'attribut de l'entité propriétaire Commandes qui pointe vers l'entité inverse Utilisateur : c'est le private $Utilisateur de l'entité Commandes
     * mappedBy --> on va se servir de l'entité Utilisateur qui est dans l'entité Commandes
     * Il faut également adapter l'entité propriétaire, pour lui dire que maintenant la relation est de type    
     * bidirectionnelle et non plus unidirectionnelle. 
     * Pour cela, il faut simplement rajouter le paramètre inversedBy dans l'annotation Many-To-One dans Commandes
     * Comme nous sommes du côté One d'un OneToMany, $commandes est un ArrayCollection ==> A PRECISER DANS LE CONSTRUCTEUR. 
     * C'est donc un addCommande /removeCommande getCommande qu'il nous faut comme getter et setter.
     * 
     * @ORM\OneToMany(targetEntity="Ecommerce\EcommerceBundle\Entity\Commandes", mappedBy="utilisateur", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $commandes;

     /**
     * --BIDIRECTIONNELLE-- un utilisateur peut avoir plusieurs adresses
     * Comme nous sommes du côté One d'un OneToMany, $utilisateursAdresses est un ArrayCollection. 
     * C'est donc un addutilisateursAdresses /removeutilisateursAdresses getutilisateursAdresses qu'il nous faut comme getter et setter.
     * 
     * @ORM\OneToMany(targetEntity="Ecommerce\EcommerceBundle\Entity\UtilisateursAdresses", mappedBy="utilisateur", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $utilisateursAdresses;


    public function __construct()
    {
        parent::__construct();
        $this->commandes   = new ArrayCollection();
        $this->utilisateursAdresses = new ArrayCollection();
    }

    /**
     * Add commande.
     *
     * @param \Ecommerce\EcommerceBundle\Entity\Commandes $commande
     *
     * @return Utilisateur
     */
    public function addCommande(\Ecommerce\EcommerceBundle\Entity\Commandes $commande)
    {
        $this->commandes[] = $commande;

        return $this;
    }

    /**
     * Remove commande.
     *
     * @param \Ecommerce\EcommerceBundle\Entity\Commandes $commande
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCommande(\Ecommerce\EcommerceBundle\Entity\Commandes $commande)
    {
        return $this->commandes->removeElement($commande);
    }

    /**
     * Get commandes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    /**
     * Add utilisateursAdress.
     *
     * @param \Ecommerce\EcommerceBundle\Entity\UtilisateursAdresses $utilisateursAdress
     *
     * @return Utilisateur
     */
    public function addUtilisateursAdress(\Ecommerce\EcommerceBundle\Entity\UtilisateursAdresses $utilisateursAdress)
    {
        $this->utilisateursAdresses[] = $utilisateursAdress;

        return $this;
    }

    /**
     * Remove utilisateursAdress.
     *
     * @param \Ecommerce\EcommerceBundle\Entity\UtilisateursAdresses $utilisateursAdress
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUtilisateursAdress(\Ecommerce\EcommerceBundle\Entity\UtilisateursAdresses $utilisateursAdress)
    {
        return $this->utilisateursAdresses->removeElement($utilisateursAdress);
    }

    /**
     * Get utilisateursAdresses.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtilisateursAdresses()
    {
        return $this->utilisateursAdresses;
    }
}
