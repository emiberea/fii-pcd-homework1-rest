<?php

namespace EB\CoreBundle\Controller;

use EB\RideBundle\Entity\RideRequestStatus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\EntityManager;

class HomeController extends Controller
{
    /**
     * @Route("/", name="eb_core_home_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
