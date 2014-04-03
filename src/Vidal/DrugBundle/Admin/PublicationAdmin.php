<?php

namespace Vidal\DrugBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;
use Vidal\DrugBundle\Transformer\DocumentsTransformer;
use Vidal\DrugBundle\Transformer\DocumentTransformer;

class PublicationAdmin extends Admin
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

	protected function configureShowField(ShowMapper $showMapper)
	{
		$showMapper
			->add('id')
			->add('title', null, array('label' => 'Заголовок'))
			->add('announce', null, array('label' => 'Анонс'))
			->add('body', null, array('label' => 'Основное содержимое'))
			->add('enabled', null, array('label' => 'Активна'))
			->add('date', null, array(
				'label'  => 'Дата создания',
				'widget' => 'single_text',
				'format' => 'd.m.Y в H:i'
			))
			->add('updated', null, array(
				'label'  => 'Дата последнего обновления',
				'widget' => 'single_text',
				'format' => 'd.m.Y в H:i'
			));
	}

	protected function configureFormFields(FormMapper $formMapper)
	{
		$em                  = $this->getModelManager()->getEntityManager($this->getSubject());
		$documentTransformer = new DocumentTransformer($em, $this->getSubject());

		$formMapper
			->add('photo', 'iphp_file', array('label' => 'Фотография', 'required' => false))
			->add('title', null, array('label' => 'Заголовок', 'required' => true))
			->add('announce', null, array('label' => 'Анонс', 'required' => true, 'attr' => array('class' => 'ckeditorfull')))
			->add('body', null, array('label' => 'Основное содержимое', 'required' => true, 'attr' => array('class' => 'ckeditorfull')))
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
			->add($formMapper->create('documents', 'text', array(
					'label'        => 'Описания препаратов',
					'required'     => false,
					'by_reference' => false,
					'attr'         => array('class' => 'doc'),
				))->addModelTransformer($documentTransformer)
			)
			->add('enabled', null, array('label' => 'Активна', 'required' => false))
			->add('date', null, array(
				'label'    => 'Дата создания',
				'data'     => new \DateTime('now'),
				'required' => true,
			));
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('id')
			->add('title', null, array('label' => 'Заголовок'))
			->add('enabled', null, array('label' => 'Активна'));
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->add('id')
			->add('title', null, array('label' => 'Заголовок'))
			->add('date', null, array(
				'label'  => 'Дата создания',
				'widget' => 'single_text',
				'format' => 'd.m.Y в H:i'
			))
			->add('enabled', null, array('label' => 'Активна', 'template' => 'VidalDrugBundle:Sonata:swap_enabled_main.html.twig'))
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