<?php

namespace Vidal\DrugBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;

class ArticleAdmin extends Admin
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

	protected function configureShowField(ShowMapper $showMapper)
	{
		$showMapper
			->add('id')
			->add('title', null, array('label' => 'Заголовок'))
			->add('link', null, array('label' => 'Адрес страницы', 'help' => 'латинские буквы и цифры, слова через тире'))
			->add('rubrique', null, array('label' => 'Рубрика'))
			->add('type', null, array('label' => 'Категория'))
			->add('announce', null, array('label' => 'Анонс'))
			->add('body', null, array('label' => 'Основное содержимое'))
			->add('forDoctor', null, array('label' => 'Только для врачей'))
			->add('atc', null, array('label' => 'Код АТХ'))
			->add('infoPage', null, array('label' => 'Информационная страница'))
			->add('enabled', null, array('label' => 'Активна'))
			->add('date', null, array('label' => 'Дата создания', 'widget' => 'single_text', 'format' => 'd.m.Y в H:i'))
			->add('updated', null, array('label' => 'Дата последнего обновления', 'widget' => 'single_text', 'format' => 'd.m.Y в H:i'));
	}

	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->add('title', null, array('label' => 'Заголовок', 'required' => true))
			->add('link', null, array('label' => 'Адрес страницы', 'required' => true, 'help' => 'латинские буквы и цифры, слова через тире'))
			->add('rubrique', null, array('label' => 'Рубрика', 'required' => true, 'empty_value' => 'выберите'))
			->add('type', null, array('label' => 'Категория', 'required' => false, 'empty_value' => 'не указано'))
			->add('announce', null, array('label' => 'Анонс', 'required' => true))
			->add('body', null, array('label' => 'Основное содержимое', 'required' => true))
			->add('forDoctor', null, array('label' => 'Только для врачей', 'required' => false))
			->add('atc', 'entity', array(
				'label'         => 'Код АТХ',
				'class'         => 'VidalDrugBundle:ATC',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('atc')
							->orderBy('atc.ATCCode', 'ASC');
					},
				'empty_value'   => 'не указано',
				'required'      => false,
			))
			->add('infoPage', 'entity', array(
				'label'         => 'Информационная страница',
				'class'         => 'VidalDrugBundle:InfoPage',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('ip')
							->orderBy('ip.RusName', 'ASC');
					},
				'empty_value'   => 'не указано',
				'required'      => false,
			))
			->add('nozologies', 'entity', array(
				'label'         => 'Заболевания МКБ-10',
				'class'         => 'VidalDrugBundle:Nozology',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('n')
							->orderBy('n.NozologyCode', 'ASC');
					},
				'multiple'      => true,
				'required'      => false,
				'empty_value'   => 'не указано',
			))
			->add('date', null, array('label' => 'Дата создания', 'required' => true))
			->add('enabled', null, array('label' => 'Активна', 'required' => true));
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('id')
			->add('title', null, array('label' => 'Заголовок'))
			->add('link', null, array('label' => 'Адрес страницы'))
			->add('rubrique', null, array('label' => 'Рубрика'))
			->add('type', null, array('label' => 'Категория'))
			->add('forDoctor', null, array('label' => 'Только для врачей'))
			->add('enabled', null, array('label' => 'Активна'));
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->add('id')
			->add('title', null, array('label' => 'Заголовок'))
			->add('link', null, array('label' => 'Адрес страницы', 'help' => 'латинские буквы и цифры, слова через тире'))
			->add('rubrique', null, array('label' => 'Рубрика'))
			->add('type', null, array('label' => 'Категория'))
			->add('atc', null, array('label' => 'Код АТХ'))
			->add('infoPage', null, array('label' => 'Информационная страница'))
			->add('created', null, array('label' => 'Дата создания', 'widget' => 'single_text', 'format' => 'd.m.Y в H:i'))
			->add('forDoctor', null, array('label' => 'Только для врачей', 'template' => 'VidalDrugBundle:Sonata:swap_forDoctor.html.twig'))
			->add('enabled', null, array('label' => 'Активна', 'template' => 'VidalDrugBundle:Sonata:swap_enabled.html.twig'))
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