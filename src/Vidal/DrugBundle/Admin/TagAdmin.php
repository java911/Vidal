<?php

namespace Vidal\DrugBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class TagAdmin extends Admin
{
	protected $datagridValues;

	public function __construct($code, $class, $baseControllerName)
	{
		parent::__construct($code, $class, $baseControllerName);

		if (!$this->hasRequest()) {
			$this->datagridValues = array(
				'_page'       => 1,
				'_per_page'   => 50,
				'_sort_order' => 'ASC',
				'_sort_by'    => 'text'
			);
		}
	}

	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('text', null, array('label' => 'Тег', 'required' => true))
			->add('search', null, array('label' => 'Выставляется по слову', 'required' => false, 'help' => 'Оставьте пустым, чтоб выставлять по тегу'))
			->add('infoPage', null, array('label' => 'Представительство', 'required' => false));
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('id')
			->add('text', null, array('label' => 'Тег'));
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->add('text', null, array('label' => 'Тег', 'template' => 'VidalDrugBundle:Sonata:tag_text.html.twig'))
			->add('search', null, array('label' => 'Выставляется по слову', 'template' => 'VidalDrugBundle:Sonata:tag_search.html.twig'))
			->add('id')
			->add('infoPage', null, array('label' => 'Представительство'))
			->add('_action', 'actions', array(
				'label'   => 'Действия',
				'actions' => array(
					'edit'     => array(),
					'delete'   => array(),
					'tagClean' => array('template' => 'VidalDrugBundle:Sonata:tag_clean.html.twig'),
					'tagSet'   => array('template' => 'VidalDrugBundle:Sonata:tag_set.html.twig'),
					'tagUnset' => array('template' => 'VidalDrugBundle:Sonata:tag_unset.html.twig'),
					'tagList'  => array('template' => 'VidalDrugBundle:Sonata:tag_list.html.twig'),
				)
			));
	}

	protected function configureRoutes(RouteCollection $collection)
	{
		$collection
			->add('tagSet')
			->add('tagClean')
			->add('tagUnset')
			->add('tagList')
		;
	}
}