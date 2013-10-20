<?php

namespace MY\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="_main")
     * @Template()
     */
    public function indexAction()
    {
        $this->get('twig.utility_extension')->setJsDependencies('site/home');


        return array();
    }
}
