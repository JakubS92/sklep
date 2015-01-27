<?php

namespace Projekt\SklepBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Projekt\SklepBundle\Entity\OrderMovie;
use Projekt\SklepBundle\Form\OrderMovieType;
use Symfony\Component\HttpFoundation\Response;
/**
 * OrderMovie controller.
 *
 * @Route("/shop")
 */
class OrderMovieController extends Controller
{

    /**
     * Lists all OrderMovie entities.
     *
     * @Route("/", name="shop")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProjektSklepBundle:OrderMovie')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Lists all OrderMovie entities.
     *
     * @Route("/myShop", name="my_shop")
     * @Method("GET")
     * @Template()
     */
    public function myShopAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProjektSklepBundle:OrderMovie')->findByUser($this->getUser());

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new OrderMovie entity.
     *
     * @Route("/", name="shop_create")
     * @Method("POST")
     * @Template("ProjektSklepBundle:OrderMovie:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new OrderMovie();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            //Kasowanie zawartości koszyka
            $this->get('session')->set('cartIDs', array());
            return $this->redirect($this->generateUrl('shop_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a OrderMovie entity.
     *
     * @param OrderMovie $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(OrderMovie $entity)
    {
        $form = $this->createForm(new OrderMovieType(), $entity, array(
            'action' => $this->generateUrl('shop_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Generuje stronę płatności na podstawie koszyka
     *
     * @Route("/payment", name="shop_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        // Pobieramy ID filmow znajdujących się w koszyku
        $cartIDs = $this->get('session')->get('cartIDs');

        // Jezeli w koszyku nie ma nic, przekieruj do strony głównej
        if (empty($cartIDs)){
           $request->getSession()->getFlashBag()->add(
                'notice',
                'Dodaj filmy do koszyka potem przejdź do płatności!'
            );
            return $this->forward('ProjektSklepBundle:Movie:index');
        }

        $em = $this->getDoctrine()->getManager();
        $movies = $em->getRepository('ProjektSklepBundle:Movie')->findById($cartIDs);

        $entity = new OrderMovie();
        // Ustawiamy, zamówienie na akutalnego użytkownika 
        // Manualnie gdyby skrypt był bardzo rozbudowany, można by pozwolić administratorom składać zamównie za kogoś
        $entity->setUser($this->getUser());
        
        $entity->setMovies($movies);

      

        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'movies' => $movies,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a OrderMovie entity.
     *
     * @Route("/{id}", name="shop_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjektSklepBundle:OrderMovie')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderMovie entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing OrderMovie entity.
     *
     * @Route("/{id}/edit", name="shop_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjektSklepBundle:OrderMovie')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderMovie entity.');
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
    * Creates a form to edit a OrderMovie entity.
    *
    * @param OrderMovie $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(OrderMovie $entity)
    {
        $form = $this->createForm(new OrderMovieType(), $entity, array(
            'action' => $this->generateUrl('shop_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing OrderMovie entity.
     *
     * @Route("/{id}", name="shop_update")
     * @Method("PUT")
     * @Template("ProjektSklepBundle:OrderMovie:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProjektSklepBundle:OrderMovie')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderMovie entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('shop_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a OrderMovie entity.
     *
     * @Route("/{id}", name="shop_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProjektSklepBundle:OrderMovie')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find OrderMovie entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('shop'));
    }

    /**
     * Creates a form to delete a OrderMovie entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('shop_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
