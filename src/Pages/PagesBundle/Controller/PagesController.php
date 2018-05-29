<?php
// src Pages/PagesBundle/Controller/pagesCOntroller.php
// Bundle séparé de e-commerce, car contient la page des mentions légales pas forcément dépendantes du bundle Ecommerce.

namespace Pages\PagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PagesController extends Controller
{
    public function menuAction()
    {
        //Inclusion de controller# différent de l'inculusion classique
        //menu.html.twig sera inclus dans le layout general base.html.twig afin d'être visible sur toutes les pages qui extend le layout general
        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository('PagesBundle:Pages')->findAll();

        return $this->render('PagesBundle:Default:pages/modulesUsed/menu.html.twig', array('pages' => $pages));
    }

    public function pagesAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('PagesBundle:Pages')->find($id);

        if(!$page) throw new NotFoundHttpException("La page liée à l'id .$id. n'existe pas");
        

        // Page de mention légale CGV
        return $this->render('PagesBundle:Default:pages/layout/pages.html.twig', array('page' => $page));
    }
}
