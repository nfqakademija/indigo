<?php

namespace Indigo\UserBundle\Controller;

//use Indigo\UserBundle\Form\SignInType;
use Indigo\UserBundle\Form\SignInType;
use Indigo\UserBundle\Form\SignUpType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Indigo\UserBundle\Entity\User;


class SignInController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
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

                $email = $form->get('email')->getData();
                $password = $form->get('password')->getData();
                $data = $this->get('indigo_user.repository')->findOneBy([
                        'email' => $email,
                        'password' => md5($password)
                    ]);

                if ($data) {

                    return $this->redirectToRoute('user_dashboard');
                } else {

                    $message = 'signin.error.no-such-user';
                }
            }

        }

        return $this->render(
            'IndigoMainBundle:Pixel:signin.html.twig',
            [
                'form' => $form->createView(),
                'error_message' => $message,
            ]
        );
    }

    /**
     * @Route("/dashboard", name="user_dashboard")
     */
    public function dashboardAction()
    {

        return $this->render('IndigoMainBundle:Pixel:index.html.twig', []);
    }

    /**
     * @Route("/signup", name="user_signup")
     */
    public function signUpAction(Request $request) {

        $message = '';

        $form= $this->createForm(new SignUpType(), new User ());

       if ($request->getMethod('POST')) {
           $form->handleRequest($request);
           if ($form->isSubmitted()) {
               if ($form->isValid()) {

                   $email =  $form->get('email')->getData();
                   $data = $this->get('indigo_user.repository')->findOneByEmail($email);


                   if ($data) { // toks useris jau egzistuoja
                       $message = 'user.error.user-exist';

                   } else {

                       $data = $form->getData();
                       $data->setUsername($email);

                       $data->cryptPassword();
                       $em = $this->get('doctrine.orm.entity_manager');
                       $em->persist($data);
                       $em->flush();
                      return $this->redirectToRoute('user_dashboard');
                   }

               } else {
                   $message = "user.error.validation";
               }

           }
       }

        return $this->render('IndigoMainBundle:Pixel:signup.html.twig', [
            'form' => $form->createView(),
            'error_message' => $message,
        ]);
    }
}
