<?php

namespace Vidal\DrugBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ArticleRubriqueAdmin extends Admin
{
	protected $datagridValues;

	public function __construct($code, $class, $baseControllerName)
	{
		parent::__construct($code, $class, $baseControllerName);

		if (!$this->hasRequest()) {
			$this->datagridValues = array(
				'_page'       => 1,
				'_per_page'   => 25,
				'_sort_order' => 'ASC',
				'_sort_by'    => 'title'
			);
		}
	}

	protected function configureShowField(ShowMapper $showMapper)
	{
		$showMapper
			->add('id')
			->add('title', null, array('label' => 'Название'))
			->add('rubrique', null, array('label' => 'Страница рубрики', 'help' => 'Принимаются латинские буквы и тире'))
			->add('priority', null, array('label' => 'Приоритет'));
	}

	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('title', null, array('label' => 'Название', 'required' => true))
			->add('rubrique', null, array(
				'label'    => 'Страница рубрики',
				'help'     => 'Принимаются латинские буквы и тире',
				'required' => true
			))
			->add('priority', null, array('label' => 'Приоритет'));
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('title', null, array('label' => 'Название'))
			->add('priority', null, array('label' => 'Приоритет'));
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->add('title', null, array('label' => 'Название'))
			->add('rubrique', null, array('label' => 'Страница рубрики'))
			->add('priority', null, array('label' => 'Приоритет'))
			->add('_action', 'actions', array(
				'label'   => 'Действия',
				'actions' => array(
					'view'   => array(),
					'edit'   => array(),
					'delete' => array(),
				)
			));
	}
}