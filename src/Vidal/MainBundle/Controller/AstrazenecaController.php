<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vidal\MainBundle\Entity\MapRegion;
use Vidal\MainBundle\Entity\MapCoord;
use Lsw\SecureControllerBundle\Annotation\Secure;

class AstrazenecaController extends Controller
{
    const PUBLICATIONS_SHOW = 5;

    /**
     * @Route("/astrazeneca", name="astrazeneca_index")
     * @Template("VidalMainBundle:Astrazeneca:index.html.twig")
     */
    public function indexAction(){
        return array();
    }

    /**
     * @Route("/astrazeneca/news", name="astrazeneca_news")
     * @Template("VidalMainBundle:Astrazeneca:news.html.twig")
     */
    public function newsAction(Request $request){
        $em = $this->getDoctrine()->getManager('drug');


        $params = array(
            'indexPage'    => true,
            'publications' => $em->getRepository('VidalDrugBundle:Publication')->findLast(self::PUBLICATIONS_SHOW),
        );

        return $params;
    }

    /**
     * @Route("/astrazeneca/new/{newId}", name="astrazeneca_new")
     * @Template("VidalMainBundle:Astrazeneca:new.html.twig")
     */
    public function shoNewAction(){}

    /**
     * @Route("/astrazeneca/map", name="astrazeneca_map")
     * @Template("VidalMainBundle:Astrazeneca:map.html.twig")
     */
    public function mapAction(){}

    /**
     * @Route("/astrazeneca/testing", name="astrazeneca_testing")
     * @Template("VidalMainBundle:Astrazeneca:testing.html.twig")
     */
    public function testingAction(){}

    /**
     * @Route("/astrazeneca/faq", name="astrazeneca_faq")
     * @Template("VidalMainBundle:Astrazeneca:faq.html.twig")
     */
    public function faqAction(){
        return array(
            'title'           => 'Вопрос-ответ',
            'questionAnswers' => $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaFaq')->findAll(),
        );
    }

}