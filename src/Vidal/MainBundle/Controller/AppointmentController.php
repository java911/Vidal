<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
//                $tmpAppointment = $this->getDoctrine()->getRepository('VidalMainBundle:Appointment')->findOneByOMSCode($appointment->getOMSCode());
//                if ($tmpAppointment->getBirthdate() == $appointment->getBirthdate()){
//                    $appointment = $tmpAppointment;
//                }else{
//                    $em->persist($appointment);
//                    $em->flush();
//                    $em->refresh($appointment);
//                }
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
}