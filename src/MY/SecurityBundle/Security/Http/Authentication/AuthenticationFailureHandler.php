<?php

namespace MY\SecurityBundle\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;

class AuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {

        if ($request->isXmlHttpRequest()) {
            $message = $exception->getMessage();
            if(strpos($message, 'An exception occurred while executing') !== false) {
                $message = 'An exception occurred while executing SQL';
            }
            return new JsonResponse(array('status' => false, 'message' => $message), 500);
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}