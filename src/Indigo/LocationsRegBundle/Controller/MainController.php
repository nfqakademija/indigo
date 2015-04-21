<?php

namespace Indigo\LocationsRegBundle\Controller;

use Indigo\LocationsRegBundle\Entity\Location;
use Indigo\LocationsRegBundle\Form\LocationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    /**
     * @Route("/main", name="main_list")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $formType = new LocationType();
        $data = new Location();

        $formFactory = $this->get('form.factory');

        $formBuilder = $formFactory->createBuilder($formType, $data);
        $formBuilder->setMethod('POST')
            ->setAction($this->generateUrl('main_list'));

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entity = $form->getData();

                $em = $this->get('doctrine.orm.entity_manager');

                //Naujus entity butinai reikia persistint entity_manger
                $em->persist($entity);
                //Visus Queryius executina flush()
                $em->flush($entity);

                return new RedirectResponse($this->generateUrl('main_list'));
            }
        }

        return [
            'locations' => $this->get('indigo_location.repository')->getAllLocations(),
            '_form' => $form->createView(),
        ];
    }
}
