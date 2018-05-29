<?php

namespace Ecommerce\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/* UploadedFile simplifie grandement les image, grâce à sa méthode move() */
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Media
 *
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="Ecommerce\EcommerceBundle\Repository\MediaRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Media 
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="updated_at",type="datetime", nullable=true) 
     */
    private $updateAt;
    /**
     * @ORM\Column(type="string",length=255) 
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(name="path", type="string", length=255, nullable=true) 
     */
    private $path;
    /**
     * On ajoute cet attribut pour y stocker le nom du fichier temporairement
     */
    private $tempFile;
    /**
     * Servira pour le formulaire, mais ce n'est pas cet attribut qui sera persisté donc pas d'annotation pour doctrine
     */
    private $file;
        
    /**
     * retourne une instance de l'objet UploadedFile
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }
    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        $this->updateAt = new \DateTime();
    }
    
    public function getUploadRootDir()
    {
        return __dir__.'/../../../../web/uploads';
    }
    
    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }
    /**
     * Méthode renseigné dans la vue pour accéder aux images
     */
    public function getAssetPath()
    {
        return 'uploads/'.$this->path;
    }
    
    /**
     * Avant de persister et updater un produit en database
     * 
     * @ORM\PrePersist()
     * @ORM\PreUpdate() 
     */
    public function preUpload()
    {
        $this->tempFile = $this->getAbsolutePath();
        $this->oldFile = $this->getPath();
        $this->updateAt = new \DateTime();
        
        if (null !== $this->file) 
        $this->path =  md5(uniqid()).'.'.$this->file->guessExtension();
    }
    /**
     * S'exécute automatiquement lorsqu'on perist et update un produit dans la database
     * 
     * @ORM\PostPersist()
     * @ORM\PostUpdate() 
     */
    public function upload()
    {
        if (null !== $this->file) {
            $this->file->move($this->getUploadRootDir(),$this->path);
            unset($this->file);
            
            if ($this->oldFile != null) 
                unlink($this->tempFile);
        }
    }
    /**
     * avant le remove
     * 
     * @ORM\PreRemove() 
     */
    public function preRemoveUpload()
    {
        $this->tempFile = $this->getAbsolutePath();
    }
    
    /**
     * au remove
     * 
     * @ORM\PostRemove() 
     */
    public function removeUpload()
    {
        if (file_exists($this->tempFile)) 
            unlink($this->tempFile);
    }




    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
         $this->path = $path;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setName($name)
    {
         $this->name = $name;
    }

    

    



    

    

}
