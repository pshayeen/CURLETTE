<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

/**
 * Shows a friendly "Access denied" page (HTTP 403) to logged-in users who try to open a page
 * their role can't see, e.g. a customer opening /dashboard or a staff member opening /users.
 */
final class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(private Environment $twig)
    {
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        $attributes = (array) $accessDeniedException->getAttributes();

        $required = match (true) {
            in_array('ROLE_ADMIN', $attributes, true) => 'admin',
            in_array('ROLE_STAFF', $attributes, true) => 'staff',
            default => null,
        };

        return new Response(
            $this->twig->render('security/access_denied.html.twig', ['required' => $required]),
            Response::HTTP_FORBIDDEN,
        );
    }
}
