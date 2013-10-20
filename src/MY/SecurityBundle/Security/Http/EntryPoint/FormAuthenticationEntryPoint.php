<?php
/*
 */

namespace MY\SecurityBundle\Security\Http\EntryPoint;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint as BaseFormAuthenticationEntryPoint;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * FormAuthenticationEntryPoint starts an authentication via a login form.
 */
class FormAuthenticationEntryPoint extends BaseFormAuthenticationEntryPoint
{
    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('status' => false, 'error' => 'Login is required'));
        }

        return parent::start($request, $authException);
    }
}
