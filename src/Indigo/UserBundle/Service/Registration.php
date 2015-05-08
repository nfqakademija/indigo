<?php

namespace Indigo\UserBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\UserBundle\Entity\Role;
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

    public function  register(User $userEntity)
    {
        $userEntity->setUsername($userEntity->getEmail());

        $encoder = $this->ef->getEncoder($userEntity);
        $encoded = $encoder->encodePassword($userEntity->getPassword(), $userEntity->getSalt());
        $userEntity->setPassword($encoded);

        $this->setRoles($userEntity);

        $this->em->persist($userEntity);
        $this->em->flush();

    }

    private function setRoles(User $user)
    {
        $role = $this->em->getRepository('IndigoUserBundle:Role')->findOneBy(['role' => Role::ROLE_USER]);

        $user->addRole($role);
    }

}