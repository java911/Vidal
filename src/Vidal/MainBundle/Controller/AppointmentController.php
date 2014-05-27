<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vidal\MainBundle\Entity\Appointment;


class AppointmentController extends Controller
{
    /**
     * @Route("/appointment", name="appointment")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em  = $this->getDoctrine()->getManager();
        $faq = new Appointment();

        $builder = $this->createFormBuilder($faq);
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
                $faq = $form->getData();
                $faq->setEnabled(0);
                $em->persist($faq);
                $em->flush();
                $em->refresh($faq);
            }
        }
        return array('form' => $form->createView());
    }
}