<?php

namespace Vidal\DrugBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;

class SubdivisionAdmin extends Admin
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
				'_sort_by'    => 'name'
			);
		}
	}

	protected function configureShowField(ShowMapper $showMapper)
	{
		$showMapper
			->add('id')
			->add('name', null, array('label' => 'Название'))
			->add('engName', null, array('label' => 'Англ. название'))
			->add('url', null, array('label' => 'Путь'))
			->add('parent', null, array('label' => 'Входит в раздел'))
			->add('title', null)
			->add('description', null)
			->add('keywords', null);
	}

	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('name', null, array('label' => 'Название', 'required' => true))
			->add('engName', null, array('label' => 'Англ. название', 'required' => true))
			->add('url', null, array('label' => 'Путь', 'required' => true))
			->add('parent', null, array('label' => 'Входит в раздел', 'required' => false))
			->add('announce', null, array('label' => 'Анонс', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('title', null)
			->add('description', null)
			->add('keywords', null);
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('id')
			->add('name', null, array('label' => 'Название', 'required' => true))
			->add('engName', null, array('label' => 'Англ. название', 'required' => true))
			->add('url', null, array('label' => 'Путь', 'required' => true))
			->add('parent', null, array('label' => 'Входит в раздел', 'required' => false));
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->add('id')
			->add('name', null, array('label' => 'Название'))
			->add('engName', null, array('label' => 'Англ. название'))
			->add('url', null, array('label' => 'Путь'))
			->add('parent', null, array('label' => 'Входит в раздел'))
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