<?php

namespace Indigo\UserBundle\Controller;

use Indigo\UserBundle\Entity\User;
use Indigo\UserBundle\Entity\ResetPassword;
use Indigo\UserBundle\Form\ChangePasswordType;
use Indigo\UserBundle\Form\PasswordRecoveryType;
use Indigo\UserBundle\Form\RemindPasswordType;
use Indigo\UserBundle\Form\LoginType;
use Indigo\UserBundle\Form\RegisterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;




class AuthController extends Controller
{
    /**
     * @Route("/", name="default_action")
     */
    public function indexAction() {
        return $this->redirectToRoute('login_action');
    }

    /**
     * @Route("/login", name="login_action")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
           return $this->redirectToRoute('user_dashboard');
        }

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

        $formLoginType = new LoginType();
        $formRemindPasswordType = new RemindPasswordType();

        $formFactory = $this->get('form.factory');

        $formBuilder = $formFactory->createBuilder($formLoginType);
        $formBuilder->setMethod('POST');
        $formBuilder->setAction($this->generateUrl('_security_check'));

        $formLogin = $formBuilder->getForm();

        $formBuilder = $formFactory->createBuilder($formRemindPasswordType);
        $formBuilder->setMethod('POST');
        $formBuilder->setAction($this->generateUrl('remind_password_action'));

        $formRemindPassword = $formBuilder->getForm();


        return [
            'form_login' => $formLogin->createView(),
            'form_remind_password' => $formRemindPassword->createView(),
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
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
//        $formType = new LoginType();
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
        return $this->redirectToRoute('user_signin');
    }

      /**
      * @Route("/register", name="register_action")
      * @Template("IndigoUserBundle:Auth:register.html.twig")
      */
    public function registerAction(Request $request) {

        $message = '';
        $userEntity = new User();
        $formType = new RegisterType();
        $formFactory = $this->get('form.factory');
        $formBuilder = $formFactory->createBuilder($formType, $userEntity);
        $form = $formBuilder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           $email =  $form->get('email')->getData();
           $data = $this->get('indigo_user.repository')->findOneByEmail($email);

           if ($data) { // toks useris jau egzistuoja
               $message = 'user.error.user-exist';
           } else {

               $data = $form->getData();
               $userEntity->setUsername($email);


               $encoder = $this->container->get('security.encoder_factory')->getEncoder($userEntity);

               $encoded = $encoder->encodePassword($userEntity->getPassword(), $userEntity->getSalt());
               $userEntity->setPassword($encoded);
               $em = $this->get('doctrine.orm.entity_manager');
               $em->persist($userEntity);
               $em->flush();

              return $this->redirectToRoute('user_dashboard');
           }
        }


        return  [
            'form' => $form->createView(),
            'error_message' => $message,
        ];
    }

    /**
     * @Route("/remind_password", name="remind_password_action")
     * @Method("POST")
     */
    public function remindPasswordAction(Request $request)
    {

        $form= $this->createForm(new RemindPasswordType(), new User ());
        $form->handleRequest($request);
        // TODO: kodel nevalid?
         if ($form->isSubmitted()) {

             $remindTo = $form->get('email')->getData();
             $em = $this->get('doctrine.orm.entity_manager');

             $user = $em->getRepository('IndigoUserBundle:User')->findOneBy(['email' => $remindTo]);

             if (is_null($user)) {
             } else {
                 $newHash = $this->generatePasswordResetHash($remindTo);
                 $currentResetPassword = $em->getRepository('IndigoUserBundle:ResetPassword')->findOneByUser($user->getId());

                 if (is_null($currentResetPassword)) {
                     $resetPassword = new ResetPassword();
                     $resetPassword->setHash($newHash);
                     $resetPassword->setActive(1);
                     $resetPassword->setUser($user->getId());
                     $em->persist($resetPassword);
                     $em->flush();
                 } else {
                     $currentResetPassword->setHash($newHash);
                     $em->flush();
                 }

                 $link = $this->generateUrl(
                     'password_recovery_action',
                     ['hash' => $newHash],
                     true
                 );

                 $template = $this->get('twig')->loadTemplate('IndigoUserBundle:Mail:passwordRecovery.html.twig');


                 $subject = $template->renderBlock('subject', []);
                 $mail_text = $template->renderBlock('mail_text', ['link' => $link]);
                 $body_html = $template->renderBlock('body_html', ['link' => $link]);

                 $mailer = $this->get('mailer');
                 $message = $mailer->createMessage()
                     ->setSubject($subject)
                     ->setFrom('info@indigo.dev')
                     ->setTo($remindTo)
                     ->setBody($body_html, 'text/html')
                     ->addPart($mail_text, 'text/plain');
                 $mailer->send($message);

                 $this->get('session')->getFlashBag()->add('notice', 'user.notice.reset_password_sent');
             }
         }
        return $this->redirectToRoute('login_action');
    }

    /**
     * @Route("/password_recovery/{hash}", name="password_recovery_action")
     */
    public function passwordRecoveryAction($hash, Request $request)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        $resetPasswordEntity = $em->getRepository('IndigoUserBundle:ResetPassword')->findOneBy([
                'hash' => $hash,
                'active' => 1
        ]);

        if (is_null($resetPasswordEntity)) {
            $this->get('session')->getFlashBag()->add('notice','user.error.password_recovery.invalid_hash');
            return $this->redirectToRoute('login_action');
        }

        $formFactory = $this->get('form.factory');

        $formBuilder = $formFactory->createBuilder(new PasswordRecoveryType(), new User());
        $formBuilder->setMethod('POST');
        $formBuilder->setAction($this->generateUrl('password_recovery_action', ['hash' => $hash]));

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userEntity = $em->getRepository('IndigoUserBundle:User')->findOneById($resetPasswordEntity->getUser());
            if (is_null($userEntity)) {

            } else {
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($userEntity);
                $userEntity->setPassword($encoder->encodePassword($form->get('password')->getData(), $userEntity->getSalt()));
                $em->persist($userEntity);
                $resetPasswordEntity->setActive(0);
                $em->persist($resetPasswordEntity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success','user.form.password_recovery.success');
                return $this->redirectToRoute('login_action');
            }
        }
        return $this->render('IndigoUserBundle:Auth:passwordRecovery.html.twig', [
            'form' => $form->createView(),
            'hash' => $hash
        ]);
    }

    private function generatePasswordResetHash($key = null) {
        return md5($key . uniqid() . time());
    }
}


