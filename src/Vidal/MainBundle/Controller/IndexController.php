<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    /**
     * @Route("/", name = "_main")
     * @Template("VidalMainBundle:Index:index.html.twig")
     */
    public function mainAction()
    {
        return array();
    }

    /**
     * @Route("/{url}", name = "page")
     * @Template
     */
    public function pageAction($url)
    {
        $page = $this->getDoctrine()->getRepository('VidalMainBundle:Page')->findOneByUrl($url);

        if (!$page) {
            return $this->render('VidalMainBundle:Page:not_found.html.twig');
        }

        return array('page' => $page, 'title' => $page->getTitle());
    }

    /**
     * Карта сайта
     * @Route("/sitemap", name = "sitemap")
     */
    public function mapAction(){
        return true;
    }
}
