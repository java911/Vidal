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
				'_sort_order' => 'DESC',
				'_sort_by'    => 'created'
			);
		}
	}

	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('banner', 'iphp_file', array('label' => 'Баннер'))
			->add('fallback', 'iphp_file', array('label' => 'Изображение, если флэш не поддерживается', 'required' => false))
			->add('link', null, array('label' => 'Ссылка', 'required' => true))
			->add('group', null, array('label' => 'Баннерное место', 'required' => true))
			->add('expires', null, array('label' => 'Осталось показов', 'help' => 'Оставьте пустым, чтоб не учитывать'))
            //->add('countries', null, array('label' => 'Страны', 'required' => false))
            //->add('cities', null, array('label' => 'города', 'required' => false))
            ->add('limitDay', null, array('label' => 'Лимит показов в день', 'required' => false))
            ->add('clickDay', null, array('label' => 'Осталось показов в день', 'required' => false))
			->add('presence', null, array('label' => 'Приоритет', 'required' => false, 'help' => 'Как часто появляется: 1-100%. Оставьте пустым, если без приоритета'))
			->add('enabled', null, array('label' => 'Активен', 'required' => false))
			->add('starts', null, array('label' => 'Дата начала'))
			->add('ends', null, array('label' => 'Дата окончания', 'required' => false, 'help' => 'Оставьте пустым, чтоб не учитывать'))
			->add('reference', null, array('label' => 'Заменяется баннером', 'required' => false, 'help' => 'Будет заменяться этим баннером'));
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
			->add('link', null, array('label' => 'Ссылка'))
			->add('group', null, array('label' => 'Баннерное место'))
			->add('clicks', null, array('label' => 'Переходов'))
			->add('displayed', null, array('label' => 'Показов'))
			->add('expires', null, array('label' => 'Осталось показов'))
            ->add('countries', null, array('label' => 'Страны', 'required' => false))
            ->add('cities', null, array('label' => 'города', 'required' => false))
			->add('presence', null, array('label' => 'Приоритет %'))
			->add('starts', null, array('label' => 'Дата начала', 'widget' => 'single_text', 'format' => 'd.m.Y в H:i'))
			->add('ends', null, array('label' => 'Дата окончания', 'widget' => 'single_text', 'format' => 'd.m.Y в H:i'))
			->add('reference', null, array('label' => 'Заменяется'))
			->add('enabled', null, array('label' => 'Активен', 'template' => 'VidalDrugBundle:Sonata:swap_enabled_main.html.twig'))
			->add('_action', 'actions', array(
				'label'   => 'Действия',
				'actions' => array(
					'edit'   => array(),
					'delete' => array(),
				)
			));
	}
}