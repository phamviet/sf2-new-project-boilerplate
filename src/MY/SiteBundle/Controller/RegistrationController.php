<?php

namespace MY\SiteBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContext;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="_register")
     * @param Request $request
     * @param bool $returnForm
     * @return \Symfony\Component\Form\FormInterface
     */
    public function registerAction(Request $request, $returnForm = false)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->createUser();

        $form = $formFactory->createForm();
        $form->add(
            'terms',
            'checkbox',
            array('property_path' => 'termsAccepted')
        );
        $form->setData($user);

        $response = new JsonResponse(array(), 500);
        $data = array(
            'status' => false
        );

        if ('POST' === $request->getMethod() && $request->isXmlHttpRequest()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user->setEnabled(true);
                $userManager->updateUser($user);

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                if (null === $res = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
                    $data['redirect'] = $url;

                } else {
                    $data['redirect'] = $res->getTargetUrl();
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                $data['status'] = true;
                $data['message'] = 'Register sucessfull';
                $response->setStatusCode(200);
            } else {
                $data['errors'] = $this->getErrorsAsArray($form);
            }

            return $response->setData($data);

        }

        if ($returnForm) {
            return $form;
        }
    }

    /**
     * Tell the user to check his email provider
     * @Route("/check-email", name="fos_user_registration_check_email")
     * @Template()
     */
    public function checkEmailAction()
    {
        $email = $this->container->get('session')->get('fos_user_send_confirmation_email/email');
        $this->container->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return array(
            'user' => $user,
        );
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     *
     * @Route("/confirm/{token}", name="fos_user_registration_confirm")
     * @Template()
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed
     *
     * @Route("/confirmed", name="fos_user_registration_confirmed")
     * @Template()
     */
    public function confirmedAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return new RedirectResponse($this->generateUrl('_main'));
    }

    protected function getErrorsAsArray(FormInterface $form, $level = 0)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $key => $child) {
            if ($child instanceof FormInterface && $err = $this->getErrorsAsArray($child, $level + 4)) {
                $errors = array_merge($err, $errors);
            }
        }

        return $errors;
    }
}
