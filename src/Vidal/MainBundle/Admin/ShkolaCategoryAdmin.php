<?php
namespace Vidal\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ShkolaCategoryAdmin extends Admin
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
				'_sort_by'    => 'priority'
			);
		}
	}

	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('title', null, array('label' => 'Заголовок'))
			->add('url', null, array('label' => 'Адрес url'))
			->add('text', null, array('label' => 'Текст', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('priority', null, array('label' => 'Приоритет', 'required' => false, 'help' => 'Поставьте повыше, если надо, чтоб была на первом месте'))
			->add('enabled', null, array('label' => 'Активна', 'required' => false));
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('id');
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->add('id')
			->add('url', null, array('label' => 'Адрес url'))
			->add('enabled', null, array('label' => 'Активен', 'template' => 'VidalDrugBundle:Sonata:swap_enabled_main.html.twig'))
			->add('priority', null, array('label' => 'Приоритет'))
			->add('_action', 'actions', array(
				'label'   => 'Действия',
				'actions' => array(
					'edit'   => array(),
					'delete' => array(),
				)
			));
	}
}