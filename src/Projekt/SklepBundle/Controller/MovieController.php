<?php

namespace Projekt\SklepBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Projekt\SklepBundle\Entity\Movie;
use Projekt\SklepBundle\Form\MovieType;

/**
 * Movie controller.
 *
 * @Route("/")
 */
class MovieController extends Controller
{

    /**
     * Lists all Movie entities.
     *
     * @Route("/", name="main_page")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProjektSklepBundle:Movie')->findAll();
        
        $deleteForms = array();
        foreach ($entities as $entity) {
            $deleteForms[] = $this->createDeleteForm($entity->getId())->createView();
        }
	// Gdy nie ma żadnych filmów, przekieruje do tworzenia
        if (count($deleteForms)<=0)
		return $this->forward("ProjektSklepBundle:Movie:new"); 
        return array(
            'entities' => $entities,
            'delete_forms' => $deleteForms,
        );
    }
    /**
     * List of all carts
     * Pobiera z sesji jakie zakupy mamy aktualnie w koszyku, pobiera po ID
     * @Route("/getCart", name="getCart")
     * @Method("GET")
     * @Template()
     */
    public function getCartAction()
    {

        //$this->get('session')->set('cartIDs', array(1,2,3,4,5,6,7,8,9,10));
        
        $cartIDs = $this->get('session')->get('cartIDs');
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProjektSklepBundle:Movie')->findById($cartIDs);

        $summaryPrice = 0.0;
        foreach ($entities as $entity)
            $summaryPrice += $entity->getPrice();

        return array(
            'itemsCount' => count($entities),
            'summaryPrice' => $summaryPrice,
        );
    }
    /**
     * List of all carts
     * Pobiera z sesji jakie zakupy mamy aktualnie w koszyku, pobiera po ID
     * @Route("/setCart/{id}", name="setCart")
     * @Method("GET")
     */
    public function setCartAction($id){

        $cartIDs = $this->get('session')->get('cartIDs');
        $cartIDs[] = $id;
        $this->get('session')->set('cartIDs', $cartIDs);

        return $this->forward('ProjektSklepBundle:Movie:index');
    }  
    /**
     * Kasuje koszyk
     * @Route("/resetCart", name="resetCart")
     * @Method("GET")
     */
    public function resetCartAction(){

       
        $cartIDs = array();
        $this->get('session')->set('cartIDs', $cartIDs);

        return $this->forward('ProjektSklepBundle:Movie:index');
    }  
    /**
     * Usuwa z koszyka
     * Pobiera z sesji jakie zakupy mamy aktualnie w koszyku, pobiera po ID
     * @Route("/delCart/{id}", name="deleteFromCart")
     * @Method("GET")
     */
    public function deleteFromCartAction($id){

        $cartIDs = $this->get('session')->get('cartIDs');
        //Usuwanie z tablicy $id
        if(($key = array_search($id, $cartIDs)) !== false) {
            unset($cartIDs[$key]);
        }
        $this->get('session')->set('cartIDs', $cartIDs);

        return $this->forward('ProjektSklepBundle:OrderMovie:new');
    }  
    /**
     * Creates a new Movie entity.
     *
     * @Route("/", name="_create")
     * @Method("POST")
     * @Template("ProjektSklepBundle:Movie:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Movie();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Movie entity.
     *
     * @param Movie $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Movie $entity)
    {
        $form = $this->createForm(new MovieType(), $entity, array(
            'action' => $this->generateUrl('_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Movie entity.
     *
     * @Route("/new", name="_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Movie();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Movie entity.
     *
     * @Route("/{id}", name="_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjektSklepBundle:Movie')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Movie entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Movie entity.
     *
     * @Route("/{id}/edit", name="_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjektSklepBundle:Movie')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Movie entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Movie entity.
    *
    * @param Movie $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Movie $entity)
    {
        $form = $this->createForm(new MovieType(), $entity, array(
            'action' => $this->generateUrl('_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Movie entity.
     *
     * @Route("/{id}", name="_update")
     * @Method("PUT")
     * @Template("ProjektSklepBundle:Movie:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjektSklepBundle:Movie')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Movie entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Movie entity.
     *
     * @Route("/{id}", name="_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProjektSklepBundle:Movie')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Movie entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('main_page'));
    }

    /**
     * Creates a form to delete a Movie entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
