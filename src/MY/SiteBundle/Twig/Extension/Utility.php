<?php
/**
 * @author Viet Pham
 */
namespace MY\SiteBundle\Twig\Extension;

use MY\EntityBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Utility extends \Twig_Extension implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     *
     * @api
     */
    protected $container;

    protected $uploadTargetUrl;
    protected $jsDependencies = array();

    /**
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->uploadTargetUrl = $this->container->getParameter('upload_target_url');
    }

    public function getFilters()
    {
        return array(
//            new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('assets_version', array($this, 'getAssetVersion')),
            new \Twig_SimpleFunction('site_vars', array($this, 'getSiteVars')),
            new \Twig_SimpleFunction('display_photo', array($this, 'displayPhoto')),
            new \Twig_SimpleFunction('display_photo', array($this, 'displayPhoto')),
        );
    }

    public function getSiteVars()
    {
        $vars = array(
            'FACEBOOK_APP_ID' => $this->container->getParameter('facebook_app_id'),
            'FACEBOOK_SCOPE'  => $this->container->getParameter('facebook_app_id'),
            'BASE_URL'        => $this->container->get('request')->getBaseUrl(),
            'BASE_PATH'       => $this->container->get('request')->getBasePath(),
            'ENV'             => $this->container->get('kernel')->getEnvironment(),
            'VERSION'         => $this->getAssetVersion(),
            'deps'            => $this->jsDependencies,
        );

        return $vars;
    }

    public function displayAvatar(User $user)
    {
        if ($user->getAvatar()) {
            return $this->uploadTargetUrl . '/' . $user->getAvatar();
        }

        if ($user->getFacebookId()) {
            return "http://graph.facebook.com/{$user->getFacebookId()}/picture?type=large";
        }

        return '/images/avatar_default.png';
    }

    public function getAssetVersion()
    {
        $env = $this->container->get('kernel')->getEnvironment();

        return $env == 'prod' ? $this->container->getParameter('assets_version') : time();
    }

    public function displayPhoto($path)
    {
        return $this->uploadTargetUrl . '/' . $path;
    }

    /**
     * @param array $jsDependencies
     */
    public function setJsDependencies($jsDependencies)
    {
        $this->jsDependencies[] = $jsDependencies;
    }

    /**
     * @return array
     */
    public function getJsDependencies()
    {
        return $this->jsDependencies;
    }


    public function getName()
    {
        return 'site_extension';
    }
}