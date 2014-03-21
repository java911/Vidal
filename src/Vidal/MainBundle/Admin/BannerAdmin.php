<?php
namespace Vidal\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BannerAdmin extends Admin
{
	protected $datagridValues;

	public function __construct($code, $class, $baseControllerName)
	{
		parent::__construct($code, $class, $baseControllerName);

		if (!$this->hasRequest()) {
			$this->datagridValues = array(
				'_page'       => 1,
				'_per_page'   => 25,
				'_sort_order' => 'DESC', // sort direction
				'_sort_by'    => 'presence' // field name
			);
		}
	}

	protected function configureShowField(ShowMapper $showMapper)
	{
		$showMapper
			->add('id')
			->add('banner', null, array('label' => 'Баннер', 'template' => 'EvrikaMainBundle:Preview:banner-show.html.twig'))
			->add('link', null, array('label' => 'Ссылка'))
			->add('adriver', null, array('label' => 'Ад-ривер'))
			->add('width', null, array('label' => 'Ширина'))
			->add('height', null, array('label' => 'Высота'))
			->add('type', null, array('label' => 'Кому показывать'))
			->add('specialties', null, array('label' => 'Показывать специальностям'))
			->add('group', null, array('label' => 'Баннерное место'))
			->add('clicks', null, array('label' => 'Переходов'))
			->add('displayed', null, array('label' => 'Показов'))
			->add('expires', null, array('label' => 'Осталось показов'))
			->add('presence', null, array('label' => 'Приоритет появления'))
			->add('enabled', null, array('label' => 'Активен'))
			->add('starts', null, array('label' => 'Дата начала'))
			->add('ends', null, array('label' => 'Дата окончания'))
			->add('created', null, array('label' => 'Дата создания', 'widget' => 'single_text', 'format' => 'd.m.Y в H:i'))
			->add('updated', null, array('label' => 'Дата обновления', 'widget' => 'single_text', 'format' => 'd.m.Y в H:i'))
			->add('swf', 'iphp_file', array('label' => 'Флеш-баннер SWF'));
	}

	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('banner', 'iphp_file', array('label' => 'Баннер'))
			->add('link', null, array('label' => 'Ссылка', 'required' => true))
			->add('adriver', null, array('label' => 'Ад-ривер', 'required' => false, 'help' => 'Ссылка на изображение ад-ривер БЕЗ ЧАСТИ &rnd=...'))
			->add('width', null, array('label' => 'Ширина', 'required' => true))
			->add('height', null, array('label' => 'Высота', 'required' => true))
			->add('type', null, array('label' => 'Кому показывать', 'required' => true))
			->add('specialties', null, array('label' => 'Показывать специальностям', 'required' => false))
			->add('group', null, array('label' => 'Баннерное место', 'required' => true))
			->add('expires', null, array('label' => 'Осталось показов', 'help' => 'Оставьте пустым, чтоб не учитывать'))
			->add('presence', null, array('label' => 'Приоритет', 'help' => 'Как часто появляется: 1-100%. Оставьте пустым, если без приоритета'))
			->add('enabled', null, array('label' => 'Активен', 'required' => false))
			->add('starts', null, array('label' => 'Дата начала'))
			->add('ends', null, array('label' => 'Дата окончания', 'help' => 'Оставьте пустым, чтоб не учитывать'))
			->add('swf', 'iphp_file', array('label' => 'Флеш-баннер SWF', 'required' => false, 'help' => 'Только .swf формат, будет отображаться вместо баннера'));
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('id')
			->add('link', null, array('label' => 'Ссылка'))
			->add('group', null, array('label' => 'Баннерное место'))
			->add('clicks', null, array('label' => 'Переходов'))
			->add('displayed', null, array('label' => 'Показов'))
			->add('expires', null, array('label' => 'Осталось показов'))
			->add('presence', null, array('label' => 'Приоритет появления'))
			->add('enabled', null, array('label' => 'Активен'))
			->add('starts', null, array('label' => 'Дата начала'))
			->add('ends', null, array('label' => 'Дата окончания'));
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->add('id')
			->add('banner', null, array('label' => 'Баннер', 'template' => 'EvrikaMainBundle:Preview:banner-list.html.twig'))
			->add('link', null, array('label' => 'Ссылка'))
			->add('specialties', null, array('label' => 'Специальности', 'template' => 'EvrikaMainBundle:Preview:banner_specialties.html.twig'))
			//->add('group', null, array('label' => 'Баннерное место'))
			->add('clicks', null, array('label' => 'Переходов'))
			->add('displayed', null, array('label' => 'Показов'))
			->add('expires', null, array('label' => 'Осталось показов'))
			->add('presence', null, array('label' => 'Приоритет %'))
			->add('starts', null, array('label' => 'Дата начала', 'widget' => 'single_text', 'format' => 'd.m.Y в H:i'))
			->add('ends', null, array('label' => 'Дата окончания', 'widget' => 'single_text', 'format' => 'd.m.Y в H:i'))
			->add('enabled', null, array('label' => 'Активен', 'template' => 'EvrikaMainBundle:Preview:swap_enabled.html.twig'))
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