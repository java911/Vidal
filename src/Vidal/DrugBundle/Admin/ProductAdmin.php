<?php
namespace Vidal\DrugBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Vidal\DrugBundle\Transformer\DocumentToStringTransformer;

class ProductAdmin extends Admin
{
	protected function configureFormFields(FormMapper $formMapper)
	{
		$subject     = $this->getSubject();
		$em          = $this->getModelManager()->getEntityManager($subject);
		$transformer = new DocumentToStringTransformer($em, $subject);

		$formMapper
			->add('RusName', 'text', array('label' => 'Название', 'required' => true))
			->add('EngName', 'text', array('label' => 'Латинское', 'required' => true))
			->add('Name', 'text', array('label' => 'URL адрес', 'required' => true))
			->add($formMapper->create('document', 'text', array(
				'label'        => 'ID документа',
				'required'     => true,
				'by_reference' => false,
			))->addModelTransformer($transformer))
			->add('ProductTypeCode', null, array('label' => 'Тип препарата', 'required' => true))
			->add('MarketStatusID', null, array('label' => 'Статус', 'required' => true))
			->add('ZipInfo', null, array('label' => 'Форма выпуска', 'required' => true))
			->add('photo', 'iphp_file', array('label' => 'Фотография временная', 'required' => false))
			->add('Composition', null, array('label' => 'Описание', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('RegistrationDate', null, array('label' => 'Дата регистрации'))
			->add('RegistrationNumber', null, array('label' => 'Номер регистрации'))
			->add('NonPrescriptionDrug', null, array('label' => 'Безрецептурный', 'required' => false))
			->add('StrongMeans', null, array('label' => 'Сильнодействующий', 'required' => false))
			->add('Poison', null, array('label' => 'Ядовитый', 'required' => false))
			->add('GNVLS', null, array('label' => 'ЖНВЛП', 'required' => false))
			->add('DLO', null, array('label' => 'ДЛО', 'required' => false))
			->add('ValidPeriod', null, array('label' => 'Срок действия', 'required' => false))
			//->add('StrCond', null, array('label' => 'Условия хранения', 'required' => false))
			->add('atcCodes', null, array('label' => 'Коды АТХ', 'required' => false))
			->add('moleculeNames', null, array('label' => 'Активные вещества', 'required' => false))
			->add('clphGroups', null, array('label' => 'Клинико-фармакологические группы', 'required' => false, 'help' => 'ClPhGroups'))
			->add('phthgroups', null, array('label' => 'Фармако-терапевтические группы', 'required' => false, 'help' => 'PhThGroups'))
			->add('productCompany', 'sonata_type_collection',
				array(
					'label'              => 'Компании',
					'by_reference'       => false,
					'cascade_validation' => true,
					'required'           => false,
				),
				array(
					'edit'         => 'inline',
					'inline'       => 'table',
					'allow_delete' => true
				)
			)
		;
	}

	// Fields to be shown on filter forms
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('ProductID', null, array('label' => 'ID'))
			->add('RusName', null, array('label' => 'Название'))
			->add('EngName', null, array('label' => 'Латинское'))
			->add('ProductTypeCode', null, array('label' => 'Тип препарата'))
			->add('MarketStatusID', null, array('label' => 'Статус'))
			->add('ZipInfo', null, array('label' => 'Форма выпуска'))
			->add('RegistrationDate', null, array('label' => 'Дата регистр.'));
	}

	// Fields to be shown on lists
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->addIdentifier('ProductID', null, array('label' => 'ID'))
			->add('RusName', null, array('label' => 'Название', 'template' => 'VidalDrugBundle:Sonata:RusName.html.twig'))
			->add('EngName', null, array('label' => 'Латинское', 'template' => 'VidalDrugBundle:Sonata:EngName.html.twig'))
			->add('ProductTypeCode', null, array('label' => 'Тип препарата'))
			->add('MarketStatusID', null, array('label' => 'Статус'))
			->add('ZipInfo', null, array('label' => 'Форма выпуска'))
			->add('RegistrationDate', null, array('label' => 'Дата регистр.'))
			->add('_action', 'actions', array(
				'label'   => 'Действия',
				'actions' => array(
					'edit'   => array(),
					'delete' => array(),
				)
			));
	}
}