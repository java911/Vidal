<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Vidal\MainBundle\Entity\Appointment;
use Vidal\MainBundle\Appointment\AppSoap;

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
                $session->set('EmiasEmail',$appointment->getEmail());
                $session->save();

                $soap = $this->createConnection();
                $specialties = $soap->getSpecialitiesInfo(array('omsNumber'=>'9988889785000068', 'birthDate'=>'2011-04-14T00:00:00', 'externalSystemId'=>'MPGU'));

                if (is_array($specialties->return)){
                    return $this->render('VidalMainBundle:Appointment:appointment_set_spec.html.twig', array('specialties' => $specialties->return));
                }
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
     * @Route("/appointment-doctors/{doctorId}", name="appointment_doctor", options={"expose"=true})
     */
    public function doctorsActions($doctorId){
        if ( $this->isAuth() == false ){ return $this->redirect($this->generateUrl('appointment')); }
        $soap = $this->createConnection();
        $doctors = $soap->getDoctorsInfo(array('omsNumber'=>'9988889785000068', 'birthDate'=>'2011-04-14T00:00:00','specialityId'=>$doctorId, 'externalSystemId'=>'MPGU'));

        return new JsonResponse(array( 'data'=> $doctors));
    }

    /**
     * @Route("/appointment-datetime/{availableResourceId}/{complexResourceId}", name="appointment_datetime", options={"expose"=true})
     */
    public function datetimeActions($availableResourceId, $complexResourceId){
        if ( $this->isAuth() == false ){ return $this->redirect($this->generateUrl('appointment')); }
        $soap = $this->createConnection();
        $datetime = $soap->getAvailableResourceScheduleInfo(array('omsNumber'=>'9988889785000068', 'birthDate'=>'2011-04-14T00:00:00','availableResourceId'=>$availableResourceId,'complexResourceId'=>$complexResourceId, 'externalSystemId'=>'MPGU'));

        return new JsonResponse(array( 'data'=> $datetime));
    }

    /**
     * @Route("/appointment-create/{availableResourceId}/{complexResourceId}/{receptionDate}/{startDate}/{endDate}", name="appointment_create", options={"expose"=true})
     */
    public function createAppointment($availableResourceId, $complexResourceId,$receptionDate,$startDate, $endDate){
        if ( $this->isAuth() == false ){ return $this->redirect($this->generateUrl('appointment')); }
        $receptionTypeCodeOrLdpTypeCode = 1863;
        $soap = $this->createConnection();
        $datetime = $soap->createAppointment(
            array(
                'omsNumber'=>'9988889785000068',
                'birthDate'=>'2011-04-14T00:00:00',
                'availableResourceId'=>$availableResourceId,
                'complexResourceId'=>$complexResourceId,
                'externalSystemId'=>'MPGU',
                '$receptionDate'=> $receptionDate,
                'startDate'=> $startDate,
                'endDate'=> $endDate,
                'receptionTypeCodeOrLdpTypeCode' => $receptionTypeCodeOrLdpTypeCode
            )
        );

        return new JsonResponse(array( 'data'=> $datetime));
    }


    /**
     * @Route("/appointment-create", name="appointment_create", options={"expose"=true})
     */
    public function createActions(){
        if ( $this->isAuth() == false ){ return $this->redirect($this->generateUrl('appointment')); }
        $soap = $this->createConnection();
        $data = $soap->getAppointmentReceptionsByPatient(
            array(
                'omsNumber'=>'9988889785000068',
                'birthDate'=>'2011-04-14T00:00:00',
                'externalSystemId'=>'MPGU'
            )
        );

        return new JsonResponse(array( 'data'=> $data));
    }

    /**
     * @Route("/appointment-delete", name="appointment_delete", options={"expose"=true})
     */
    public function deleteAction($appointmentId){
        if ( $this->isAuth() == false ){ return $this->redirect($this->generateUrl('appointment')); }
        $soap = $this->cancelAppointment();
        $data = $soap->getAppointmentReceptionsByPatient(
            array(
                'omsNumber'=>'9988889785000068',
                'birthDate'=>'2011-04-14T00:00:00',
                'appointmentId'=>$appointmentId,
                'externalSystemId'=>'MPGU'
            )
        );

        return new JsonResponse(array( 'data'=> $data));
    }


    protected function createConnection(){
        $cert="/var/www/vidal/web/sert/testSSLClient.pem"; //Сертификат
        $wsdl="https://mosmedzdrav.ru:10002/emias-soap-service/PGUServicesInfo2?wsdl"; //Адрес wdsl сервиса
        $pass = 'testSSLClient';
        if (!is_file($cert)){
            echo 'file certificate not found!';
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
        $sp = new \SoapClient($wsdl ,array(
            'local_cert' => $cert,
            'passphrase'    => $pass,
            'stream_context' => $sslContext,
            'trace' => 0,
            'exceptions' => 0,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'wsdl_cache_enabled' => false
        ));

        return $sp;
    }

}