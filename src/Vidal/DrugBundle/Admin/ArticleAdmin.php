<?php

namespace Vidal\DrugBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;
use Vidal\DrugBundle\Transformer\DocumentTransformer;
use Vidal\DrugBundle\Transformer\TagTransformer;

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
				'_sort_by'    => 'date'
			);
		}
	}

	protected function configureFormFields(FormMapper $formMapper)
	{
		$em                  = $this->getModelManager()->getEntityManager($this->getSubject());
		$subject             = $this->getSubject();
		$documentTransformer = new DocumentTransformer($em, $subject);
		$tagTransformer      = new TagTransformer($em, $subject);

		$formMapper
			->add('title', null, array('label' => 'Заголовок', 'required' => true))
			->add('link', null, array('label' => 'Адрес страницы', 'required' => false, 'help' => 'латинские буквы и цифры, слова через тире. Оставьте пустым для автогенерации'))
			->add('rubrique', null, array(
				'label'         => 'Рубрика',
				'required'      => true,
				'empty_value'   => 'выберите',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('r')
							->orderBy('r.title', 'ASC');
					},
			))
			->add('type', null, array('label' => 'Категория', 'required' => false, 'empty_value' => 'не указано'))
			->add('states', null, array('label' => 'Болезни', 'required' => false, 'empty_value' => 'не указано'))

			->add('priority', null, array('label' => 'Приоритет', 'required' => false, 'help' => 'Закреплено на главной по приоритету. Оставьте пустым, чтоб снять приоритет'))
			->add('announce', null, array('label' => 'Анонс', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('body', null, array('label' => 'Основное содержимое', 'required' => true, 'attr' => array('class' => 'ckeditorfull')))
			->add('tags', null, array('label' => 'Теги', 'required' => false, 'help' => 'Выберите существующие теги или добавьте новый ниже'))
			->add($formMapper->create('hidden', 'text', array(
					'label'        => 'Создать теги через ;',
					'required'     => false,
					'by_reference' => false,
				))->addModelTransformer($tagTransformer)
			)
			->add('atcCodes', 'entity', array(
				'label'         => 'Коды АТХ',
				'class'         => 'VidalDrugBundle:ATC',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('atc')
							->orderBy('atc.ATCCode', 'ASC');
					},
				'empty_value'   => 'не указано',
				'required'      => false,
				'multiple'      => true,
				'attr'          => array('placeholder' => 'Начните вводить название или код'),
			))
			->add('molecules', 'entity', array(
				'label'         => 'Активные вещества',
				'help'          => '(Molecule)',
				'class'         => 'VidalDrugBundle:Molecule',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('m')
							->orderBy('m.RusName', 'ASC');
					},
				'empty_value'   => 'не указано',
				'required'      => false,
				'multiple'      => true,
				'attr'          => array('placeholder' => 'Начните вводить название или ID'),
			))
			->add('infoPages', 'entity', array(
				'label'         => 'Представительства',
				'help'          => 'Информационные страницы (InfoPage)',
				'class'         => 'VidalDrugBundle:InfoPage',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('ip')
							->where("ip.CountryEditionCode = 'RUS'")
							->orderBy('ip.RusName', 'ASC');
					},
				'empty_value'   => 'не указано',
				'required'      => false,
				'multiple'      => true,
				'attr'          => array('placeholder' => 'Начните вводить название или ID'),
			))
			->add('nozologies', 'entity', array(
				'label'         => 'Заболевания МКБ-10',
				'help'          => '(Nozology)',
				'class'         => 'VidalDrugBundle:Nozology',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('n')
							->orderBy('n.NozologyCode', 'ASC');
					},
				'required'      => false,
				'empty_value'   => 'не указано',
				'multiple'      => true,
				'attr'          => array('placeholder' => 'Начните вводить название или код'),
			))
			->add($formMapper->create('hidden2', 'text', array(
					'label'        => 'Описания препаратов',
					'required'     => false,
					'by_reference' => false,
					'attr'         => array('class' => 'doc'),
				))->addModelTransformer($documentTransformer)
			)
			->add('date', null, array('label' => 'Дата создания', 'required' => true, 'years' => range(2000, date('Y'))))
			->add('synonym', null, array('label' => 'Синонимы', 'required' => false, 'help' => 'Через ;'))
			->add('metaTitle', null, array('label' => 'Мета заголовок', 'required' => false))
			->add('metaDescription', null, array('label' => 'Мета описание', 'required' => false))
			->add('metaKeywords', null, array('label' => 'Мета ключевые слова', 'required' => false))
			->add('video', 'iphp_file', array('label' => 'Видео', 'required' => false, 'help' => 'Загрузить флеш-видео в формате .flv'))
			->add('links', 'sonata_type_collection',
				array(
					'label'        => 'Ссылки Купить в интернет-аптеке',
					'required'     => false,
					'by_reference' => false,
					'type_options' => array('btn_delete' => true, 'btn_add' => true),
				),
				array(
					'edit'   => 'inline',
					'inline' => 'table'
				)
			)
			->add('anons', null, array('label' => 'Отображать в анонсе', 'required' => false))
			->add('anonsPriority', null, array('label' => 'Приоритет в анонсе'))
			->add('testMode', null, array('label' => 'В режиме тестирования', 'required' => false, 'help' => 'видно только если в конец url-адреса дописать ?test'))
			->add('enabled', null, array('label' => 'Активна', 'required' => false));
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('id')
			->add('title', null, array('label' => 'Заголовок'))
			->add('link', null, array('label' => 'Адрес страницы'))
			->add('rubrique', null, array(
				'label'         => 'Рубрика',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('r')
							->orderBy('r.title', 'ASC');
					},
			))
			->add('type', null, array('label' => 'Категория'))
			->add('priority', null, array('label' => 'Приоритет'))
			->add('anons', null, array('label' => 'Отображать в анонсе', 'help' => 'В разделе специалистам'))
			->add('anonsPriority', null, array('label' => 'Приоритет в анонсе'))
			->add('testMode', null, array('label' => 'В режиме тестирования'))
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
			->add('tags', null, array('label' => 'Теги', 'template' => 'VidalDrugBundle:Sonata:tags.html.twig'))
			->add('anons', null, array('label' => 'в анонсе', 'template' => 'VidalDrugBundle:Sonata:swap_anons.html.twig'))
			->add('anonsPriority', null, array('label' => 'Приоритет в анонсе'))
			->add('date', null, array('label' => 'Дата создания', 'widget' => 'single_text', 'format' => 'd.m.Y в H:i'))
			->add('enabled', null, array('label' => 'Активна', 'template' => 'VidalDrugBundle:Sonata:swap_enabled.html.twig'))
			->add('_action', 'actions', array(
				'label'   => 'Действия',
				'actions' => array(
					'edit'   => array(),
					'delete' => array(),
				)
			));
	}
}