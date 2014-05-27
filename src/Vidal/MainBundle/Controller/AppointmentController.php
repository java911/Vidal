<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppointmentController extends Controller
{
    /**
     * @Route("/appointment", name="appointment")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em  = $this->getDoctrine()->getManager();
        $faq = new QuestionAnswer();
        if ($this->getUser()) {
            $faq->setAuthorFirstName($this->getUser()->getFirstname());
            $faq->setAuthorEmail($this->getUser()->getUsername());
        }
        $builder = $this->createFormBuilder($faq);
        $builder
            ->add('authorFirstName', null, array('label' => 'Ваше имя'))
            ->add('authorEmail', null, array('label' => 'Ваш e-mail'))
            ->add('question', null, array('label' => 'Вопрос'))
            ->add('captcha', 'captcha', array('label' => 'Введите код с картинки'))
            ->add('submit', 'submit', array('label' => 'Задать вопрос', 'attr' => array('class' => 'btn')));

        $form = $builder->getForm();
        $form->handleRequest($request);
        $t = 0;
        if ($request->isMethod('POST')) {
            $t = 1;
            if ($form->isValid()) {
                $t   = 2;
                $faq = $form->getData();
                $faq->setEnabled(0);
                $em->persist($faq);
                $em->flush();
                $em->refresh($faq);
            }
        }
        return array();
    }
}