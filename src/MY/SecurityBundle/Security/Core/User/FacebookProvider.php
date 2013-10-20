<?php
/**
 * Author: Viet Pham Thanh
 * Created at: 1/26/13 12:36 AM
 */
namespace MY\SecurityBundle\Security\Core\User;

use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException,
    Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ValidatorInterface;
use FOS\UserBundle\Security\UserProvider;

class FacebookProvider extends UserProvider
{
    /**
     * @var \Facebook
     */
    protected $facebook;

    public function __construct(\BaseFacebook $facebook, UserManager $userManager, ValidatorInterface $validator)
    {
        parent::__construct($userManager);

        $this->facebook = $facebook;
        $this->validator = $validator;
    }

    public function findUserByFbId($fbId)
    {
        return $this->userManager->findUserBy(array('facebookId' => $fbId));
    }

    public function loadUserByUsername($username)
    {
        $user = $this->findUserByFbId($username);
        try {
            $fbdata = $this->facebook->api('/me');
        } catch (\FacebookApiException $e) {
            $fbdata = null;
        }

        if (!empty($fbdata)) {
            if (empty($user)) {
                $user = $this->userManager->createUser();
                $user->setEnabled(true);
                $user->setPassword('');
            }

            // TODO use http://developers.facebook.com/docs/api/realtime
            $user->setFBData($fbdata);

            if (count($this->validator->validate($user, 'Facebook'))) {
                // TODO: the user was found obviously, but doesnt match our expectations, do something smart
                throw new UsernameNotFoundException('The facebook user could not be stored');
            }
            $this->userManager->updateUser($user);
        }

        if (empty($user)) {
            throw new UsernameNotFoundException('The user is not authenticated on facebook');
        }

        return $user;
    }
}
