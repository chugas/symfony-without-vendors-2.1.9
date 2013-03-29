<?php

namespace Application\Sonata\CustomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CustomBundle:Default:index.html.twig', array('name' => $name));
    }
}
