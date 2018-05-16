<?php
/**
 * Created by PhpStorm.
 * User: Claus Perbony
 * Date: 15/05/2018
 * Time: 14:30
 */

namespace App\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="admin_default")
     * @Template("@Admin/default/index.html.twig")
     */
    public function index()
    {

    }
}