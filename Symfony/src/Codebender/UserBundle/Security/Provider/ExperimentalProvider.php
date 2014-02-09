<?php

namespace Codebender\UserBundle\Security\Provider;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ExperimentalProvider implements UserProviderInterface
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('No user with name "%s" was found.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->userManager->refreshUser($user);
    }

    public function supportsClass($class)
    {
        return $this->userManager->supportsClass($class);
    }
}

