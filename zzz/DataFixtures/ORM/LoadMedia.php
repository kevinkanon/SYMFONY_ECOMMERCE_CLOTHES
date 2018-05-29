<?php

// src/Ecommerce/EcommerceBundle/DataFixtures/ORM/LoadMedia.php
namespace Ecommerce\EcommerceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ecommerce\EcommerceBundle\Entity\Media;

class LoadMedia extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $media1 = new Media();
        $media1->setAlt('Légumes');
        $media1->setPath('https://www.vulgaris-medical.com/sites/default/files/styles/big-lightbox/public/field/image/actualites/2017/07/05/impact-de-la-cuisson-sur-les-bienfaits-des-legumes.jpg');
        $manager->persist($media1);

        $media2 = new Media();
        $media2->setAlt('Fruits');
        $media2->setPath('https://www.livemint.com/rf/Image-621x414/LiveMint/Period2/2017/10/31/Photos/Processed/fruits-kFLF--621x414@LiveMint.jpg');
        $manager->persist($media2);

        $media3 = new Media();
        $media3->setAlt('Poivron rouge');
        $media3->setPath('https://kissmychef.com/wp-content/uploads/2017/02/istock_paprikas.jpg');
        $manager->persist($media3);

        $media4 = new Media();
        $media4->setAlt('Piment');
        $media4->setPath('http://patatietpatata.org/wp-content/uploads/2015/09/piment-de-cayenne.jpg');
        $manager->persist($media4);

        $media5 = new Media();
        $media5->setAlt('Tomate');
        $media5->setPath('https://www.lesfruitsetlegumesfrais.com/_upload/cache/ressources/produits/tomate/tomate_-_copie_346_346_filled.jpg');
        $manager->persist($media5);

        $media6 = new Media();
        $media6->setAlt('Poivrons vert');
        $media6->setPath('http://marchecastor.com/67-thickbox_default/poivron-vert-marche-castor-en-ligne.jpg');
        $manager->persist($media6);

        $media7 = new Media();
        $media7->setAlt('Raisin');
        $media7->setPath('https://media.gerbeaud.net/2009/raisins.jpg');
        $manager->persist($media7);

        $media8 = new Media();
        $media8->setAlt('Orange');
        $media8->setPath('https://www.lesfruitsetlegumesfrais.com/_upload/cache/ressources/produits/orange/orange_346_346_filled.jpg');
        $manager->persist($media8);




        $this->addReference('media1', $media1);
        $this->addReference('media2', $media2);
        $this->addReference('media3', $media3);
        $this->addReference('media4', $media4);
        $this->addReference('media5', $media5);
        $this->addReference('media6', $media6);
        $this->addReference('media7', $media7);
        $this->addReference('media8', $media8);

        $manager->flush();
    }

    /**
     * Méthode retourner en 1re position
     *
     */
    public function getOrder()
    {
        return 1;
    }
}




