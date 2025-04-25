<?php

/*namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();
        $roles = $user->getRoles();  // On récupère un tableau de rôles

        // Nous prenons simplement le premier rôle pour la redirection
        // Vous pouvez personnaliser cette logique si vous voulez une logique plus complexe
        $role = reset($roles); // Le premier rôle de l'utilisateur

        switch ($role) {
            case 'administrateur':
                return new RedirectResponse($this->router->generate('dashboard'));
            case 'patient':
                return new RedirectResponse($this->router->generate('patient_dashboard'));
            case 'medecin':
                return new RedirectResponse($this->router->generate('medecin_dashboard'));
            case 'pharmacien':
                return new RedirectResponse($this->router->generate('pharmacien_dashboard'));
            default:
                return new RedirectResponse($this->router->generate('app_default'));
        }
    }
}
*/


namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMINISTRATEUR', $roles, true)) {
            return new RedirectResponse($this->router->generate('dashboard'));
        }
        if (in_array('ROLE_MEDECIN', $roles, true)) {
            return new RedirectResponse($this->router->generate('medecin_dashboard'));
        }
        if (in_array('ROLE_PATIENT', $roles, true)) {
            return new RedirectResponse($this->router->generate('patient_dashboard'));
        }
        if (in_array('ROLE_PHARMACIEN', $roles, true)) {
            return new RedirectResponse($this->router->generate('pharmacien_dashboard'));
        }

        return new RedirectResponse($this->router->generate('app_home'));
    }
}