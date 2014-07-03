<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vidal\MainBundle\Entity\AstrazenecaFaq;
use Vidal\MainBundle\Entity\AstrazenecaRegion;
use Vidal\MainBundle\Entity\AstrazenecaMap;
use Lsw\SecureControllerBundle\Annotation\Secure;

class AstrazenecaController extends Controller
{

    /**
     * @Route("/astrazeneca", name="astrazeneca_index")
     * @Template("VidalMainBundle:Astrazeneca:index.html.twig")
     */
    public function indexAction(){
        return array();
    }

    /**
     * @Route("/astrazeneca/articles", name="astrazeneca_news")
     * @Template("VidalMainBundle:Astrazeneca:news.html.twig")
     */
    public function newsAction(Request $request){
        $em = $this->getDoctrine()->getManager();


        $params = array(
            'indexPage'    => true,
            'publications' => $em->getRepository('VidalMainBundle:AstrazenecaNew')->findAll(),
        );

        return $params;
    }

    /**
     * @Route("/astrazeneca/article/{newId}", name="astrazeneca_new")
     * @Template("VidalMainBundle:Astrazeneca:new.html.twig")
     */
    public function showNewAction($newId){
        $em = $this->getDoctrine()->getManager();
        $publication = $em->getRepository('VidalMainBundle:AstrazenecaNew')->findOneById($newId);

        if (!$publication) {
            throw $this->createNotFoundException();
        }

        return array(
            'publication' => $publication,
            'menu_left'   => 'news',
            'title'       => $this->strip($publication->getTitle()) . ' | Новости',
        );
    }

    /**
     * @Route("/astrazeneca/map", name="astrazeneca_map")
     * @Template("VidalMainBundle:Astrazeneca:map.html.twig")
     */
    public function mapAction(){
        return array();
    }

    /**
     * @Route("/astrazeneca/map-ajax", name="astrazeneca_map_xml", options={"expose"=true})
     * @Template("VidalMainBundle:Astrazeneca:map_xml.html.twig")
     */
    public function mapXmlAction(){
        $coords[0] = $this->getRequest()->query->get('x1');
        $coords[1] = $this->getRequest()->query->get('y1');
        $coords[2] = $this->getRequest()->query->get('x2');
        $coords[3] = $this->getRequest()->query->get('y2');
        $zoom = $this->getRequest()->query->get('z');

        if ($zoom <= 5){
            $coords = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaRegion')->findAll();
        }else{
            $coords = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaMap')->findCoords($coords);
        }
        return array('coords' => $coords);
    }

    /**
     * @Route("/astrazeneca/testing", name="astrazeneca_testing")
     * @Template("VidalMainBundle:Astrazeneca:test.html.twig")
     */
    public function testingAction(Request $request){

        $tests = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaTest')->findAll();

        return array(
            'tests' => $tests,
        );
    }

    /**
     * @Route("/astrazeneca/testing-ajax/{step}", name="astrazeneca_testing_ajax", options={"expose"=true})
     */
    public function testingAjaxAction(Request $request, $step){

        $question = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaTest')->findAll();
        if (isset($question[$step-1])){
            $question = $question[$step-1];
        }else{
            $question = null;
        }

        return new Response($question->getTitle());
    }

    /**
     * @Route("/astrazeneca/faq", name="astrazeneca_faq")
     * @Template("VidalMainBundle:Astrazeneca:faq.html.twig")
     */
    public function faqAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $faq = new AstrazenecaFaq();

        $builder = $this->createFormBuilder($faq);
        $builder
            ->add('authorFirstName', null, array('label' => 'Ваше имя'))
            ->add('authorEmail', null, array('label' => 'Ваш e-mail'))
            ->add('question', null, array('label' => 'Вопрос', 'attr' => array('class' => 'ckeditor')))
            ->add('captcha', 'captcha', array('label' => 'Введите код с картинки'))
            ->add('submit', 'submit', array('label' => 'Задать вопрос', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()){
                $faq = $form->getData();
                $faq->setEnabled(0);
                $em->persist($faq);
                $em->flush();
                $em->refresh($faq);
            }
        }

        return array(
            'title'           => 'Вопрос-ответ',
            'questionAnswers' => $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaFaq')->findByEnabled(1),
            'form'  => $form->createView()
        );
    }

    /**
     * @Route("/astrazeneca/admin/faq", name="admin_astrazeneca_faq")
     * @Template("VidalMainBundle:Astrazeneca:admin_faq.html.twig")
     */
    public function adminFaqListAction(){
//        if ($this->getUser()->isGranted('ROLE_ZENECA') == false){
//            $this->redirect($this->generateUrl('index'));
//        }
        $faqs = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaFaq')->findAll();
        return array('faqs' => $faqs);
    }

    /**
     * @Route("/astrazeneca/admin/faq/add", name="admin_astrazeneca_faq_add")
     * @Template("VidalMainBundle:Astrazeneca:admin_faq_edit.html.twig")
     */
    public function adminFaqAddAction(Request $request){

//        if ($this->getUser()->isGranted('ROLE_ZENECA') == false){
//            $this->redirect($this->generateUrl('index'));
//        }

        $em = $this->getDoctrine()->getManager();
        $faq = new AstrazenecaFaq();

        $builder = $this->createFormBuilder($faq);
        $builder
            ->add('question', null, array('label' => 'Вопрос', 'attr' => array('class' => 'ckeditor')))
            ->add('answer', null, array('label' => 'Ответ', 'attr' => array('class' => 'ckeditor')))
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $faq = $form->getData();
                $em->persist($faq);
                $em->flush();
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/astrazeneca/admin/faq/{faqId}", name="admin_astrazeneca_faq_edit")
     * @Template("VidalMainBundle:Astrazeneca:admin_faq_edit.html.twig")
     */
    public function adminFaqEditAction(Request $request, $faqId){

//        if ($this->getUser()->isGranted('ROLE_ZENECA') == false){
//            $this->redirect($this->generateUrl('index'));
//        }

        $em = $this->getDoctrine()->getManager();
        $faq = $em->getRepository('VidalMainBundle:AstrazenecaFaq')->findOneById($faqId);

        $builder = $this->createFormBuilder($faq);
        $builder
            ->add('question', null, array('label' => 'Вопрос'))
            ->add('answer', null, array('label' => 'Ответ'))

            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn')));

        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $faq = $form->getData();
                $em->flush($faq);
            }
        }
        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/astrazeneca/admin/faq/delete/{faqId}", name="admin_astrazeneca_faq_delete")
     * @Template("VidalMainBundle:Astrazeneca:admin_faq_edit.html.twig")
     */
    public function adminFaqDeleteAction(Request $request, $faqId){

//        if ($this->getUser()->isGranted('ROLE_ZENECA') == false){
//            $this->redirect($this->generateUrl('index'));
//        }

        $em = $this->getDoctrine()->getManager();
        $faq = $em->getRepository('VidalMainBundle:AstrazenecaFaq')->findOneById($faqId);

        $em->remove($faq);
        $em->flush();


        return $this->redirect($this->generateUrl('astrazeneca_faq'));
    }

    private function strip($string)
    {
        $pat = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i', '/&amp;/');
        $rep = array('', '', '&');

        return preg_replace($pat, $rep, $string);
    }

    /**
     * @Route("/zgetMapHintContent/{id}", name="zgetMapHintContent", options={"expose"=true})
     */
    public function getMapHintContentaction($id)
    {
        $em    = $this->getDoctrine()->getManager();
        $coord = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaMap')->findOneById($id);
            $html = $coord->getTitle();
        return new Response($html);
    }

    /**
     * @Route("/zgetMapBalloonContent/{id}", name="zgetMapBalloonContent", options={"expose"=true})
     */
    public function getMapBalloonContent($id)
    {
        $coord = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaMap')->findOneById($id);
        $html = $coord->getAdr();

        return new Response($html);
    }

}