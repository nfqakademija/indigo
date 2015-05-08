<?php

namespace Indigo\UserBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Service\TeamCreate;
use Indigo\UserBundle\Entity\Role;
use Indigo\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class Registration
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     *
     */
    private $ef;

    /**
     * @var TeamCreate
     */
    private $teamCreateService;

    /**
     * @param EntityManagerInterface $em
     * @param EncoderFactoryInterface $ef
     * @param TeamCreate $teamCreateService
     */
    public function __construct (EntityManagerInterface $em, EncoderFactoryInterface $ef, TeamCreate $teamCreateService)
    {
        $this->em = $em;
        $this->ef = $ef;
        $this->teamCreateService = $teamCreateService;
    }

    public function register(User $userEntity)
    {
        $userEntity->setUsername($userEntity->getEmail());

        $encoder = $this->ef->getEncoder($userEntity);
        $encoded = $encoder->encodePassword($userEntity->getPassword(), $userEntity->getSalt());
        $userEntity->setPassword($encoded);

        $this->teamCreateService->createSinglePlayerTeam($userEntity);
        $this->setRoles($userEntity);

        $this->em->persist($userEntity);
        $this->em->flush();
    }

    /**
     * @param User $user
     */
    private function setRoles(User $user)
    {
        $role = $this->em->getRepository('IndigoUserBundle:Role')->findOneBy(['role' => Role::ROLE_USER]);

        $user->addRole($role);
    }

}