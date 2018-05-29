<?php
// src/Ecommerce/EcommerceBundle/Controller/ProduitsAdminController.php

namespace Ecommerce\EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ecommerce\EcommerceBundle\Entity\Produits;
use Ecommerce\EcommerceBundle\Form\ProduitsType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Produit controller.
 *
 */
class ProduitsAdminController extends Controller
{
      /**
     * Lists all Produits entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('EcommerceBundle:Produits')->findAll();

        return $this->render('EcommerceBundle:Administration:Produits/layout/index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Produits entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Produits();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $em->persist($entity);
            $em->flush();
            return $this->redirectToRoute('adminProduits_show', array('id' => $entity->getId()));
        }

        return $this->render('EcommerceBundle:Administration:Produits/layout/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
    * Creates a form to create a Produits entity.
    *
    * @param Produits $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Produits $entity)
    {
        $form = $this->createForm(ProduitsType::class, $entity);
        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }
    /**
     * Displays a form to create a new Produits entity.
     *
     */
    public function newAction()
    {
        $entity = new Produits();
        $form = $this->createCreateForm($entity);
        
        return $this->render('EcommerceBundle:Administration:Produits/layout/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Finds and displays a Produits entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:Produits')->find($id);

        if (!$entity) 
            throw $this->createNotFoundException('Unable to find Produits entity.');
        
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EcommerceBundle:Administration:Produits/layout/show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        
        ));
    }
    /**
     * Displays a form to edit an existing Produits entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:Produits')->find($id);

        if (!$entity) 
            throw $this->createNotFoundException('Impossible de trouver le produit.');
        
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EcommerceBundle:Administration:Produits/layout/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
    * Creates a form to edit a Produits entity.
    *
    * @param Produits $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Produits $entity)
    {
        $form = $this->createForm(ProduitsType::class, $entity);
        $form->add('submit', SubmitType::class, array('label' => 'Mettre à jour')); // EDIT

        return $form;
    }
    /**
     * Edits an existing Produits entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:Produits')->find($id);

        if (!$entity) 
            throw $this->createNotFoundException('Impossible de trouver le produit..');
        
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            $em->flush();
            return $this->redirectToROute('adminProduits_edit', array('id' => $id));
        }

        return $this->render('EcommerceBundle:Administration:Produits/layout/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Produits entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EcommerceBundle:Produits')->find($id);
            
            if (!$entity) 
                throw $this->createNotFoundException('Impossible de trouver et supprimer le produit');
            
            $em->remove($entity);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('adminProduits'));
    }
    /**
     * Creates a form to delete a Produits entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('adminProduits_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Supprimer')) //DELETE
            ->getForm()
        ;
    }
}
