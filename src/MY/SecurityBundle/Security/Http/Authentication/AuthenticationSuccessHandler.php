<?php

namespace MY\SecurityBundle\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($request->isXmlHttpRequest()) {
            $redirect = $this->httpUtils->generateUri($request, $this->determineTargetUrl($request));
            $status = true;
            $message = 'Login Success';
            return new JsonResponse(compact('redirect', 'status', 'message'));
        }

        return parent::onAuthenticationSuccess($request, $token);
    }
}