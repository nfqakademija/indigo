<?php

namespace Indigo\UserBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\UserBundle\Entity\User;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class Registration
{

    private $em;

    /**
     *
     */
    private $ef;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct (EntityManagerInterface $em, EncoderFactoryInterface $ef)
    {
        $this->em = $em;
        $this->ef = $ef;
    }

    public function register(Form $form)
    {
        if ($form->isSubmitted() && $form->isValid()) {

            $email =  $form->get('email')->getData();
            $data = $this->em->getRepository('IndigoUserBundle:User')->findOneByEmail($email);
            if ($data != null) {

                return [
                    'status' => 0,
                    'error_message' => 'user.error.user-exist'
                ];
            }

            $userEntity = new User();
            $userEntity->setUsername($email);
            $userEntity->setEmail($email);
            $encoder = $this->ef->getEncoder($userEntity);
            $encoded = $encoder->encodePassword($userEntity->getPassword(), $userEntity->getSalt());
            $userEntity->setPassword($encoded);
            $this->em->persist($userEntity);
            $this->em->flush();

            return [
              'status' => 1
            ];
        }

        return [
            'status' => 0
        ];


/*        $message = '';
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

                return $this->redirectToRoute('indigo_ui_dashboard');
            }
        }


        return  [
            'form' => $form->createView(),
            'error_message' => $message,
        ];
        */
    }

}