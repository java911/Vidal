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
     * @Template("VidalMainBundle:Index:page.html.twig")
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
        /**
         * Список страниц
         * Список лекарств
         * Список статей
         * Список новостей
         * Список симптомов ?
         * memcached
         */

        return true;
    }

    /**
     * Новости с Эврики список ( Выбирается по полю "isVidal" )
     * @Route("/news" , name="news")
     * @Template("VidalMainBundle:Index:news.html.twig")
     */
    public function newsAction(){
        return array();
    }

    /**
     * Раскрытая новость с Эврики
     * @Route("/new/{newId}" , name="new")
     * @Template("VidalMainBundle:Index:new.html.twig")
     */
    public function newAction(){
        return array();
    }


}
