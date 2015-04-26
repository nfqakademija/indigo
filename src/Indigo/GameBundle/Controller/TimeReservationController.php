<?php

namespace Indigo\GameBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Indigo\GameBundle\Entity\GameTime;

class TimeReservationController extends Controller
{

    /**
     * @Route("/", name="reservating_time")
     * @Method("POST")
     * @Template("IndigoGameBundle:TimeReservation:index.html.twig")
     */
    public function indexAction(Request $request){
        $entity = new GameTime();
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('IndigoGameBundle:GameTime')->findAll();
        $form = $this->createFormBuilder($entity)
            ->add('startAt', 'text', [
                'label' => false,
                //'widget' => 'single_text',
                'read_only' => true,
                'attr' => [
                    'placeholder' => 'Pasirinkite jums tinkamą laiką'
                ]
            ])
            ->add('submit', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid()){
            $em->persist($entity);
            $em->flush();
        }

        return array(
            'entities' => $entities,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/get_list_of_dates", name="get_list_of_dates")
     * @Method("POST")
     * @Template("")
     */
    public function getListOfDatesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
         "SELECT gt FROM Indigo\GameBundle\Entity\GameTime gt"
        );

        $return  = new JsonResponse($query->getArrayResult());

        return $return;
    }

    /**
     * @Route("/clicked", name="clicked")
     * @Method("POST")
     * @Template("")
     */
    public function clickedAction()
    {
        $entity = new GameTime();
        $em = $this->getDoctrine()->getManager();
        $entity->setConfirmed(1);
        $entity->setStartAt(new \DateTime());
        $em->persist($entity);
        $em->flush();

        return array();
    }

}
