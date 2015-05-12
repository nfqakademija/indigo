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
    public function loginAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            return $this->redirectToRoute('indigo_ui_dashboard');
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
            if ($error) {

                $error = $this->get('translator')->trans('user.error.bad_credentials');
            }
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $formLoginType = new LoginType();


        $formFactory = $this->get('form.factory');

        $formBuilder = $formFactory->createBuilder($formLoginType);
        $formBuilder->setMethod('POST');
        $formBuilder->setAction($this->generateUrl('login_check'));

        $formLogin = $formBuilder->getForm();


        $formRemindPasswordType = new RemindPasswordType();
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
     * @Route("/login_check", name="login_check")
     */
    public function securityCheckAction()
    {
        // this controller will not be executed,
        // as the route is handled by the Security system
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
    public function registerAction(Request $request)
    {
        $userEntity = new User();
        $formFactory = $this->get('form.factory');
        $formBuilder = $formFactory->createBuilder(new RegisterType(), $userEntity);
        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $this->get('indigo_user.registration.service')->register($userEntity);
                return $this->redirectToRoute('indigo_ui_dashboard');
            }
        }

        return [ 'form' => $form->createView() ];
    }

    /**
     * @Route("/remind_password", name="remind_password_action")
     * @Method("POST")
     */
    public function remindPasswordAction(Request $request)
    {
        $form= $this->createForm(new RemindPasswordType(), new User ());
        $form->handleRequest($request);
         if ($form->isSubmitted()) {

             $remindTo = $form->get('email')->getData();
             $em = $this->get('doctrine.orm.entity_manager');

             $user = $em->getRepository('IndigoUserBundle:User')->findOneBy(['email' => $remindTo]);

             if (!is_null($user)) {

                 $newHash = $this->generatePasswordResetHash($remindTo);
                 $currentResetPassword = $em->getRepository('IndigoUserBundle:ResetPassword')->findOneByUser($user->getId());

                 if (is_null($currentResetPassword)) {

                     $resetPassword = new ResetPassword();
                     $resetPassword->setHash($newHash);
                     $resetPassword->setActive(1);
                     $resetPassword->setUser($user);
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


                 $subject = $template->renderBlock('mail_subject', []);
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

                 $this->get('session')->getFlashBag()->add('notice', $this->get('translator')->trans('user.notice.reset_password_sent'));
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
            $this->get('session')->getFlashBag()->add('notice', $this->get('translator')->trans('user.error.password_recovery.invalid_hash'));
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
            if (!is_null($userEntity)) {

                $encoder = $this->container->get('security.encoder_factory')->getEncoder($userEntity);
                $userEntity->setPassword($encoder->encodePassword($form->get('password')->getData(), $userEntity->getSalt()));
                $em->persist($userEntity);
                $resetPasswordEntity->setActive(0);
                $em->persist($resetPasswordEntity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('user.form.password_recovery.success'));
                return $this->redirectToRoute('login_action');
            }
        }

        return $this->render('IndigoUserBundle:Auth:passwordRecovery.html.twig', [
            'form' => $form->createView(),
            'hash' => $hash
        ]);
    }

    /**
     * @param null $key
     * @return string
     */
    private function generatePasswordResetHash($key = null)
    {

        return md5($key . uniqid() . time());
    }
}


