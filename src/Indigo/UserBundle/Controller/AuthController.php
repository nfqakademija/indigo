<?php

namespace Indigo\UserBundle\Controller;

use Indigo\UserBundle\Entity\Role;
use Indigo\UserBundle\Entity\User;
use Indigo\UserBundle\Form\SignInType;
use Indigo\UserBundle\Form\SignUpType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class AuthController extends Controller
{
    /**
     * @Route("/login", name="login_action")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $formType = new SignInType();

        $formFactory = $this->get('form.factory');

        $formBuilder = $formFactory->createBuilder($formType);
        $formBuilder->setMethod('POST');
        $formBuilder->setAction($this->generateUrl('_security_check'));

        $form = $formBuilder->getForm();

        return [
            'form' => $form->createView(),
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            //'last_username' => $lastUsername,
            'error_message' => $error,
        ];
    }

    /**
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction()
    {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }

    /**
     * @Route("/signin", name="user_signin")
     * @Template()
     */
    public function signinAction(Request $request)
    {
//        $message = '';
//
//        $formType = new SignInType();
//
//        $formFactory = $this->get('form.factory');
//
//        $formBuilder = $formFactory->createBuilder($formType, $formData);
//        $formBuilder->setMethod('POST');
//
//        $form = $formBuilder->getForm();
//
//        if ($request->isMethod('POST')) {
//            $form->handleRequest($request);
//
//            if ($form->isValid()) {
//
//                $email = $form->get('email')->getData();
//                $password = $form->get('password')->getData();
//                $data = $this->get('indigo_user.repository')->findOneBy([
//                        'email' => $email,
//                        'password' => md5($password)
//                    ]);
//
//                if ($data) {
//
//                    return $this->redirectToRoute('user_dashboard');
//                } else {
//
//                    $message = 'signin.error.no-such-user';
//                }
//            }
//
//        }
//
//        return $this->render(
//            'IndigoMainBundle:Pixel:signin.html.twig',
//            [
//                'form' => $form->createView(),
//                'error_message' => $message,
//            ]
//        );
    }

    /**
     * @Route("/logout", name="logout_action")
     */
    public function logoutAction()
    {
        $this->container->get('request_stack')->getCurrentRequest()->getSession()->clear();
        $uri = $this->generateUrl('user_signin', array(), UrlGenerator::ABSOLUTE_URL);

        return $this->redirect($uri);
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
                       $user = new User();
                       $data = $form->getData();
                       $data->setUsername($email);
                       $encoder = $this->container->get('security.password_encoder');
                       $encoded = $encoder->encodePassword($user, $data->getPassword());
                       $data->setPassword($encoded);
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


