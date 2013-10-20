<?php

namespace MY\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/security_test", name="_security_test")
     * @Template()
     */
    public function indexAction()
    {
        return array('name' => 'Mr. Security');
    }
}
