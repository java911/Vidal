<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Tests\Extension\Core\DataTransformer\DateTimeToArrayTransformerTest;
use Vidal\MainBundle\Entity\DiseaseParty;
use Vidal\MainBundle\Entity\DiseaseStateArticle;

class SymptomController extends Controller{

    /**
     * @Route("/symptoms", name="symptom_page")
     * @Template("VidalMainBundle:Disease:page.html.twig")
     */
    public function pageSymptomAction(){
        return array();
    }

    /**
     * @Route("/symptom/{party}", name="render_symptom", defaults = { "party"="all" }, options={"expose"=true})
     * @Template("VidalMainBundle:Disease:symptomList.html.twig")
     */
    public function symptomListAction($party = 'all'){
        if ($party == 'all'){
            $symptoms = $this->getDoctrine()->getRepository('VidalMainBundle:DiseaseSymptom')->findAll();
        }else{
            $party = $this->getDoctrine()->getRepository('VidalMainBundle:DiseaseParty')->findOneById($party);
            $symptoms = $party->getSymptoms();
        }
        return array(
            'symptoms'   => $symptoms,
        );
    }

    /**
     * @Route("/diseases/{symptom}", name="render_disease", defaults = { "symptom"="all" }, options={"expose"=true})
     * @Template("VidalMainBundle:Disease:diseaseList.html.twig")
     */
    public function diseaseListAction($symptom = 'all'){
        if ($symptom == 'all'){
            $diseases = $this->getDoctrine()->getRepository('VidalMainBundle:Disease')->findAll();
        }else{
            $symptom = $this->getDoctrine()->getRepository('VidalMainBundle:DiseaseSymptom')->findOneById($symptom);
            $diseases = $symptom->getDiseases();
        }
        return array(
            'diseases'   => $diseases,
        );
    }

    /**
     * @Route("/state/{disease}", name="render_state", defaults = { "disease" = 0 }, options={"expose"=true})
     * @Template("VidalMainBundle:Disease:stateList.html.twig")
     */
    public function stateListAction($disease = 'all'){
        if ($disease == 'all'){
            $states = $this->getDoctrine()->getRepository('VidalMainBundle:DiseaseState')->findAll();
        }else{
            $disease = $this->getDoctrine()->getRepository('VidalMainBundle:Disease')->findOneById($disease);
            $states = $disease->getStates();
        }
        return array(
            'states'   => $states,
        );
    }

    /**
     * @Route("/symptom-body/{sex}/{party}", name="symptom_body", defaults = { "sex"="1" , "party"="all" }, options={"expose"=true})
     * @Template("VidalMainBundle:Disease:body.html.twig")
     */
    public function bodyAction($sex = 1, $party = 'all'){
        return array(
            'sex'   => $sex,
            'party'   => $party,
        );
    }

    /**
     * @Route("/generatesymptomarticletarget")
     */
    public function stateArticleAction(){
        echo 'sd';
        $max = Array();
        $max[141] =	26;
        $max[223] =	5;
        $max[170] =	6;
        $max[135] =	240;
        $max[254] =	436;
        $max[218] =	269;
        $max[107] =	247;
        $max[371] =	261;
        $max[239] =	411;
        $max[193] =	216;
        $max[294] =	296;
        $max[325] =	106;
        $max[310] =	49;
        $max[74] =	38;
        $max[378] =	330;
        $max[353] =	297;
        $max[75] =	155;
        $max[76] =	412;
        $max[78] =	412;
        $max[264] =	308;
        $max[143] =	28;
        $max[144] =	174;
        $max[122] =	59;
        $max[158] =	202;
        $max[88] =	124;
        $max[87] =	27;
        $max[198] =	159;
        $max[375] =	371;
        $max[206] =	225;
        $max[71] =	331;
        $max[109] =	131;
        $max[91] =	182;
        $max[212] =	175;
        $max[236] =	208;
        $max[77] =	60;
        $max[227] =	207;
        $max[132] =	129;
        $max[253] =	368;
        $max[105] =	82;
        $max[136] =	563;
        $max[84] =	409;
        $max[131] =	405;
        $max[241] =	153;
        $max[348] =	557;
        $max[123] =	105;
        $max[362] =	251;
        $max[318] =	138;
        $max[161] =	24;
        $max[115] =	54;
        $max[171] =	186;
        $max[142] =	416;
        $max[201] =	211;
        $max[154] =	277;
        $max[172] =	153;
        $max[138] =	241;
        $max[181] =	209;
        $max[369] =	170;
        $max[189] =	95;
        $max[296] =	252;
        $max[96] =	12;
        $max[286] =	244;
        $max[303] =	180;
        $max[121] =	22;
        $max[335] =	128;
        $max[94] =	137;
        $max[350] =	320;
        $max[307] =	40;
        $max[358] =	41;
        $max[268] =	221;
        $max[269] =	204;
        $max[329] =	107;
        $max[366] =	222;
        $max[370] =	223;
        $max[349] =	178;
        $max[343] =	220;
        $max[326] =	152;
        $max[326] =	253;
        $max[347] =	264;
        $max[340] =	219;
        $max[221] =	265;
        $max[211] =	17;
        $max[79] =	377;
        $max[145] =	20;
        $max[145] =	21;
        $max[285] =	184;
        $max[134] =	413;
        $max[150] =	135;
        $max[139] =	39;
        $max[328] =	209;
        $max[231] =	266;
        $max[90] =	492;
        $max[93] =	206;
        $max[322] =	167;
        $max[164] =	262;
        $max[164] =	263;
        $max[159] =	451;
        $max[99] =	436;
        $max[357] =	132;
        $max[72] =	332;
        $max[298] =	52;
        $max[101] =	248;
        $max[263] =	479;
        $max[377] =	8;
        $max[194] =	143;
        $max[249] =	11;
        $max[276] =	488;
        $max[225] =	416;
        $max[293] =	211;
        $max[156] =	277;
        $max[83] =	378;
        $max[338] =	145;
        $max[81] =	379;
        $max[235] =	147;
        $max[261] =	279;
        $max[137] =	239;
        $max[155] =	74;
        $max[127] =	203;
        $max[363] =	251;
        $max[160] =	267;
        $max[186] =	215;
        $max[183] =	215;
        $max[331] =	213;

        $em = $this->getDoctrine()->getManager();
        foreach ( $max as $key => $val){
            $DiseaseState = $em->getRepository('VidalMainBundle:DiseaseState')->findOneById($key);
            $DiseaseStateArticle = new DiseaseStateArticle();
            $DiseaseStateArticle->setArticleId($val);
            $DiseaseStateArticle->setDiseaseState($DiseaseState);
            $em->persist($DiseaseStateArticle);
            $em->flush();

        }
        echo "it's all";
        exit;

    }

}