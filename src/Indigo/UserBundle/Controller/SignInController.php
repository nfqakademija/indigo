<?php

namespace Indigo\UserBundle\Controller;

//use Indigo\UserBundle\Form\SignInType;
use Indigo\UserBundle\Form\SignInType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Indigo\UserBundle\Entity\User;


class SignInController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction() {
        return $this->forward("IndigoUserBundle:SignIn:signin");
    }

    /**
     * @Route("/signin", name="user_signin")
     */

    public function signinAction(Request $request)
    {
        $message = '';

        $formType = new SignInType();
        $formData = new User();

        $formFactory = $this->get('form.factory');

        $formBuilder = $formFactory->createBuilder($formType, $formData);
        $formBuilder->setMethod('POST');

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $email = $form["email"]->getData();
                $password = $form["password"]->getData();
                $data = $this->get('indigo_user.repository')->getUser($email, $password);
                if (is_array($data) && count($data) > 0 && (int) $data[0]['id'] > 0) {

                    return $this->redirectToRoute('user_dashboard');
                } else {
                    $message  = 'signin.error.no-such-user';
                }
            }

        }
        return $this->render('IndigoMainBundle:Pixel:signin.html.twig', [
            'form' => $form->createView(),
            'error_message' => $message,
        ]);
    }

    /**
     * @Route("/dashboard", name="user_dashboard")
     */
    public function dashboardAction() {

        return $this->render('IndigoMainBundle:Pixel:index.html.twig', []);
    }
}
