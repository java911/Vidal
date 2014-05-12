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

		$usingChoices = array(
			'Can'  => 'Возможно применение',
			'Care' => 'C осторожностью применяется',
			'Not'  => 'Противопоказан',
		);

		$formMapper
			->add('RusName', 'text', array('label' => 'Название', 'required' => true))
			->add('EngName', 'text', array('label' => 'Латинское', 'required' => true))
			->add('Name', 'text', array('label' => 'URL адрес', 'required' => true))
			->add('ArticleID', 'choice', array('label' => 'Тип документа', 'help' => 'ArticleID', 'required' => true, 'choices' => $articleChoices))			->add('CompiledComposition', null, array('label' => 'Описание', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('YearEdition', null, array('label' => 'Год выпуска'))
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
			->add('PharmDelivery', null, array('label' => 'Условия отпуска из аптек', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('SpecialInstruction', null, array('label' => 'Особые указания', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('PregnancyUsing', 'choice', array('label' => 'При беременности', 'required' => false, 'choices' => $usingChoices, 'empty_value' => 'выберите'))
			->add('NursingUsing', 'choice', array('label' => 'При кормлении грудью', 'required' => false, 'choices' => $usingChoices, 'empty_value' => 'выберите'))
			->add('RenalInsufUsing', 'choice', array('label' => 'При нарушениях функции почек', 'required' => false, 'choices' => $usingChoices, 'empty_value' => 'выберите'))
			->add('RenalInsuf', null, array('label' => 'Нарушения функции почек', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('HepatoInsufUsing', 'choice', array('label' => 'При нарушениях функции печени', 'required' => false, 'choices' => $usingChoices, 'empty_value' => 'выберите'))
			->add('HepatoInsuf', null, array('label' => 'Нарушение функции печени', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('ElderlyInsufUsing', 'choice', array('label' => 'Примение пожилыми пациентами', 'required' => false, 'choices' => $usingChoices, 'empty_value' => 'выберите'))
			->add('ElderlyInsuf', null, array('label' => 'Использование пожилыми пациентами', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('ChildInsufUsing', 'choice', array('label' => 'Применение детьми', 'required' => false, 'choices' => $usingChoices, 'empty_value' => 'выберите'))
			->add('ChildInsuf', null, array('label' => 'Использование детьми', 'required' => false, 'attr' => array('class' => 'ckeditorfull')))
			->add('atcCodes', null, array('label' => 'Коды АТХ', 'required' => false))
			->add('nozologies', null, array('label' => 'Нозологические указатели', 'required' => false, 'help' => 'МКБ-10 (Nozology)'))
			->add('clphPointers', null, array('label' => 'Клинико-фармакологические указатели', 'required' => false))
			->add('infoPages', null, array('label' => 'Представительства'))
			->add('molecules', null, array('label' => 'Активные вещества'))
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