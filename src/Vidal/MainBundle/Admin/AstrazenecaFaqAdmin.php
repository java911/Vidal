<?php

namespace Vidal\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AstrazenecaFaqAdmin extends Admin
{
    protected $datagridValues;

    public function __construct($code, $class, $baseControllerName)
    {
        parent::__construct($code, $class, $baseControllerName);

        if (!$this->hasRequest()) {
            $this->datagridValues = array(
                '_page'       => 1,
                '_per_page'   => 25,
                '_sort_order' => 'DESC',
                '_sort_by'    => 'created'
            );
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('authorFirstName', null, array('label' => 'Имя автора', 'required' => true))
            ->add('authorEmail', null, array('label' => 'Email автора', 'required' => true))
            ->add('question', null, array('label' => 'Вопрос', 'required' => true, 'attr' => array('class' => 'ckeditorfull')))
            ->add('answer', null, array('label' => 'Ответ', 'required' => true, 'attr' => array('class' => 'ckeditorfull')))

            ->add('enabled', null, array('label' => 'Активен', 'required' => false))
            ->add('created', null, array(
                'label'    => 'Дата создания',
                'data'     => new \DateTime('now'),
                'required' => true,
            ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('authorFirstName', null, array('label' => 'Имя автора'))
            ->add('authorEmail', null, array('label' => 'Email Автора'))
            ->add('enabled', null, array('label' => 'Активнен'));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('authorFirstName', null, array('label' => 'Имя автора', 'required' => true))
            ->add('authorEmail', null, array('label' => 'Email автора', 'required' => true))
            ->add('enabled', null, array('label' => 'Активен', 'required' => false))
            ->add('created', null, array(
                'label'    => 'Дата создания',
                'data'     => new \DateTime('now'),
                'required' => true,
            ))
            ->add('_action', 'actions', array(
                'label'   => 'Действия',
                'actions' => array(
                    'edit'   => array(),
                    'delete' => array(),
                )
            ));
    }
}