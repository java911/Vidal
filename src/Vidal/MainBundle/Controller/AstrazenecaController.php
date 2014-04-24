<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vidal\MainBundle\Entity\AstrazenecaFaq;
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
     * @Template("VidalMainBundle:Astrazeneca:test.html.twig")
     */
    public function testingAction(){
        $questions = $this->getDoctrine()->getRepository('VidalMainBundle:AstrazenecaTest')->findAll();

        $question = array_rand($questions);

        return array(
            'question' => $question,
        );
    }

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

}