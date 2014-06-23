<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Vidal\MainBundle\Entity\Appointment;


class AppointmentController extends Controller
{

    # Если пользователь авторизован возвращает TRUE, иначе FALSE
    protected function isAuth(){

        $session = new Session();
        $emiasBirthdate = $session->get('EmiasBirthdate');
        $emiasOms = $session->get('EmiasOms');
        if ( $emiasBirthdate == null || $emiasOms == null ){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @Route("/appointment", name="appointment")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em  = $this->getDoctrine()->getManager();
        $appointment = new Appointment();

        $builder = $this->createFormBuilder($appointment);
        $builder
            ->add('email', null, array('label' => 'E-mail'))
            ->add('OMSCode', null, array('label' => 'Номер полиса ОМС'))
            ->add('birthdate', 'date', array(
                'label'  => 'Дата рождения',
                'years'  => range(date('Y') - 111, date('Y')),
                'format' => 'dd MMMM yyyy',
            ))
//            ->add('captcha', 'captcha', array('label' => 'Введите код с картинки'))
            ->add('submit', 'submit', array('label' => 'Продолжить', 'attr' => array('class' => 'btn')));
        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $appointment = $form->getData();
                # Авторизовываем полльзователя
                $session = $request->getSession();
                $session->set('EmiasBirthdate',$appointment->getBirthdate());
                $session->set('EmiasOms',$appointment->getOMSCode());
                $session->save();
                return $this->redirect($this->generateUrl('appointment_list'));
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * Список действительных записей
     * @Route("/appointment-list", name="appointment_list")
     * @Template()
     */
    public function listAction(){
        if ( $this->isAuth() == false ){ return $this->redirect($this->generateUrl('appointment')); }
        $appointmentList = $this->getDoctrine()->getRepository('VidalMainBundle:Appointment')->findByStatus(1);
        return array('appointmentList' => $appointmentList );
    }

    /**
     * @Route("/appointment-create", name="appointment_create")
     */
    public function createAction(){
        if ( $this->isAuth() == false ){ return $this->redirect($this->generateUrl('appointment')); }

        return array();
    }

    /**
     * Отклонение заявки
     * @Route("/appointment-remove/{appointmentId}", name="appointment_remove")
     * @Template()
     */
    public function removeAction($appointmentId){

    }

    /**
     * Получаем спициальности доступных врачей с ЕМИАСа
     */
    protected  function getSpecialty(){}

    /**
     * Получаем докторов по выбранной специальности с ЕМИАСа
     */
    protected function getDoctorsInfo($specialityId){}

    /**
     * Получаем расписание выбраного врача
     */
    protected function getAvailableResourceScsheduleInfo($availableResourceId, $complexResourceId){}

    /**
     * Создание заявки
     * @param $availableResourceId
     * @param $complexResourceId
     * @param $receptionDate ( Дата регистрации )
     * @param $startTime ( Время начала приема )
     * @param $endTime ( Время окончания приема )
     */
    protected function createAppointment($availableResourceId,$complexResourceId,$receptionDate,$startTime,$endTime){

    }

    /**
     * @Route("/soaptest", name="soaptest")
     */
    public function soapTestAction(){
        $cert="/var/www/vidal/web/sert/testSSLClient.pem"; //Сертификат
        $wsdl="https://mosmedzdrav.ru:10002/emias-soap-sercvice/PGUServicesInfo2?wsdl"; //Адрес wdsl сервиса
//        $wsdl="http://schemas.xmlsoap.org/soap/envelope"; //Адрес wdsl сервиса
        $loc = "https://mosmedzdrav.ru:10002/emias-soap-sercvice/PGUServicesInfo2"; //Адрес точки доступа
        $pass = 'testSSLClient';
        if (!is_file($cert)){
            echo 'sd';
            exit;
        }
        $sslOptions = array(
            'ssl' => array(
                'cafile' => "/var/www/vidal/web/sert/RootMedCA.cer",
                'allow_self_signed' => true,
                'verify_peer' => false,
            ),
        );
        $sslContext = stream_context_create($sslOptions);
        $sp = new \SoapClient(null,array(
            'local_cert' => $cert,
            'passphrase'    => $pass,
            'stream_context' => $sslContext,
            'trace' => 1,
            'exceptions' => 1,
            'soap_version' => SOAP_1_2,
            'location' =>$loc,
            'uri' =>$wsdl,
            "authentication"=>SOAP_AUTHENTICATION_DIGEST,
            "style"=>SOAP_DOCUMENT,
            "use"=>SOAP_LITERAL,
            'cache_wsdl' => WSDL_CACHE_NONE
        ));
        try{
//            var_dump($sp);
            var_dump($sp->__getFunctions());
            exit;
//            $data = $sp->__soapCall("getSpecialitiesInfo",array($omsNumber,$birthDate,$externalSystemId));
            $omsNumber = 'R25090000002789';
            $omsSeries = '';
            $birthDate = '2083-08-17';
//            $birthDate = new \DateTime('2083-08-17');
            $externalSystemId = 'MIS';
            try{
//                $data = $sp->getSpecialitiesInfo($omsNumber,$birthDate,$externalSystemId);
//                $data = $sp->__soapCall("getSpecialitiesInfo",array($omsNumber,$birthDate,$externalSystemId));
                print_r($data);
            }catch (SoapFault $e){
                echo $e->getMessage();
                exit;
            }
        }  catch (SoapFault $e) {
            echo "<h2>Exception Error!</h2>";
            echo $sp->__getLastRequest();
            echo get_class($e);
            echo $e->getMessage();
        }
    }
}