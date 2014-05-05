<?php
// src/Vidal/DrugBundle/Admin/PostAdmin.php

namespace Vidal\DrugBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class DocumentAdmin extends Admin
{
	// Fields to be shown on create/edit forms
	protected function configureFormFields(FormMapper $formMapper)
	{
		$articleChoices = array(
			'2' => 'Полные описания под торговыми наименованиями',
			'3' => 'Короткие описания под торговыми наименованиями',
			'1' => 'Описания активных веществ',
			'5' => 'Инструкция по применению лекарственного препарата',
			'4' => 'Официальная типовая клинико-фармакологическая статья',
			'6' => 'Описания БАДов',
		);

		$formMapper
			->add('RusName', 'text', array('label' => 'Название', 'required' => true))
			->add('EngName', 'text', array('label' => 'Латинское', 'required' => true))
			->add('Name', 'text', array('label' => 'URL адрес', 'required' => true))
			->add('ArticleID', 'choice', array('label' => 'Тип документа', 'help' => 'ArticleID', 'required' => true, 'choices' => $articleChoices))
			->add('CountryEditionCode', null, array('label' => 'Издание', 'required' => true))
			->add('CompiledComposition', null, array('label' => 'Описание', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('CompaniesDescription', null, array('label' => 'Описание компаний', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('ClPhGrDescription', null, array('label' => 'Клинико-фарм. группа', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('PhInfluence', null, array('label' => 'Фарм. действие', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('PhKinetics', null, array('label' => 'Фармакокинетика', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('Dosage', null, array('label' => 'Режим дозирования', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('OverDosage', null, array('label' => 'Передозировка', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('Interaction', null, array('label' => 'Лекарственное взаимодействие', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('Lactation', null, array('label' => 'Применение при беременности и кормлении грудью', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('SideEffects', null, array('label' => 'Побочное действие', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('StorageCondition', null, array('label' => 'Условия и сроки хранения', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('Indication', null, array('label' => 'Показания', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('ContraIndication', null, array('label' => 'Противопоказания', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('SpecialInstruction', null, array('label' => 'Особые указания', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('PregnancyUsing', null, array('label' => 'Использование при беременности'))
			->add('NursingUsing', null, array('label' => 'инструкция пользования'))
			->add('RenalInsuf', null, array('label' => 'Почечная недостаточность'))
			->add('RenalInsufUsing', null, array('label' => 'Использование при почечной недостаточности'))
			->add('HepatoInsuf', null, array('label' => 'Печеночная недостаточность'))
			->add('HepatoInsufUsing', null, array('label' => 'Использование при печеночной недостаточности'))
			->add('PharmDelivery', null, array('label' => 'Условия отпуска из аптек'))
			->add('WithoutRenalInsuf', null, array('label' => 'Без почечной недостаточности'))
			->add('WithoutHepatoInsuf', null, array('label' => 'Без печеночной недостаточности'))
			->add('ElderlyInsuf', null, array('label' => 'Использование престарелыми'))
			->add('ElderlyInsufUsing', null, array('label' => 'Использование престарелыми'))
			->add('ChildInsuf', null, array('label' => 'Возрастное ограничение'))
			->add('ChildInsufUsing', null, array('label' => 'Возрастное ограничение 2'))
			->add('ed', null, array('label' => 'издание'))
			->add('DateOfIncludingText', null, array('label' => 'Дата создания'))
			->add('DateTextModified', null, array('label' => 'дата модификации'))

			//            ->add('atcCodes')
			//            ->add('productDocument')
			//            ->add('nozologies')
			//            ->add('clphPointers')
			//            ->add('documentEditions')

			//            ->add('atcCodes', 'entity', array('class' => 'Vidal\DrugBundle\Entity\documentoc_atc'))
			//            ->add('productDocument', 'entity', array('class' => 'Vidal\DrugBundle\Entity\ProductDocument'))
			//            ->add('nozologies', 'entity', array('class' => 'Vidal\DrugBundle\Entity\document_indicnozology'))
			//            ->add('clphPointers', 'entity', array('class' => 'Vidal\DrugBundle\Entity\NozologyCode'))
			//            ->add('documentEditions', 'entity', array('class' => 'Vidal\DrugBundle\Entity\DocumentEdition'))
			//            ->add('moleculeDocuments', 'entity', array('class' => 'Vidal\DrugBundle\Entity\MoleculeDocument'))
		;
	}

	// Fields to be shown on filter forms
	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('RusName')
			->add('ArticleID');
	}

	// Fields to be shown on lists
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->addIdentifier('ArticleID')
			->add('RusName', null, array('label' => 'Название на русском'))
			->add('EngName', null, array('label' => 'Название на англиском'))
			->add('_action', 'actions', array(
				'label'   => 'Действия',
				'actions' => array(
					'show'   => array(),
					'edit'   => array(),
					'delete' => array(),
				)
			));;
	}
}