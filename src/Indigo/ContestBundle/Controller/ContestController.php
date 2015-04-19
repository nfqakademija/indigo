<?php

namespace Indigo\ContestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Indigo\ContestBundle\Entity\Contest;
use Indigo\ContestBundle\Form\ContestType;

/**
 * Contest controller.
 *
 * @Route("/contest")
 *
 */
class ContestController extends Controller
{

    /**
     * Lists all Contest entities.
     *
     * @Route("/", name="contest")
     * @Method("GET")
     * @Template("IndigoContestBundle:Contest:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('IndigoContestBundle:Contest')->findAll();

        $this->setImageUrlGlobal("");

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Contest entity.
     *
     * @Route("/", name="contest_create")
     * @Method("POST")
     * @Template("IndigoContestBundle:Contest:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Contest();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->uploadImage();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contest_show', array('id' => $entity->getId())));
        }

        $this->setImageUrlGlobal("");

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a form to create a Contest entity.
     *
     * @param Contest $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Contest $entity)
    {
        $form = $this->createForm(new ContestType(), $entity, array(
            'action' => $this->generateUrl('contest_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit',
            array(
                'label' => 'create_contest.form.submit_create',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ));

        return $form;
    }

    /**
     * Displays a form to create a new Contest entity.
     *
     * @Route("/new/", name="contest_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Contest();
        $form   = $this->createCreateForm($entity);

        $this->setImageUrlGlobal($entity->getPathForImage());

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Contest entity.
     *
     * @Route("/{id}", name="contest_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IndigoContestBundle:Contest')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contest entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $this->setImageUrlGlobal($entity->getPathForImage());

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Contest entity.
     *
     * @Route("/edit/{id}/", name="contest_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IndigoContestBundle:Contest')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contest entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        $this->setImageUrlGlobal($entity->getPathForImage());

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Contest entity.
     *
     * @param Contest $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Contest $entity)
    {
        $form = $this->createForm(new ContestType(), $entity, array(
            'action' => $this->generateUrl('contest_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', [
            'label' => 'update',
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ]);
        return $form;
    }
    /**
     * Edits an existing Contest entity.
     *
     * @Route("/{id}/", name="contest_update")
     * @Method("PUT")
     * @Template("IndigoContestBundle:Contest:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IndigoContestBundle:Contest')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contest entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->uploadImage();
            $em->flush();

            return $this->redirect($this->generateUrl('contest_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Contest entity.
     *
     * @Route("/{id}/", name="contest_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('IndigoContestBundle:Contest')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Contest entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('contest'));
    }

    /**
     * Creates a form to delete a Contest entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contest_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', [
                'label' => 'delete',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]])
            ->getForm();
    }

    private function setImageUrlGlobal($url){
        $imageName = $url ? $url : "contest-logo.jpg";
        $this->get('twig')->addGlobal('imagePath', $imageName);
    }
}
