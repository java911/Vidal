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
        $formMapper
            ->add('RusName', 'text', array('label' => 'Русское название'))
            ->add('EngName', 'text', array('label' => 'Латинское название'))
            ->add('CompiledComposition', 'textarea', array('label' => 'Описание'))
            ->add('ArticleID', 'text', array('label' => 'Артикуль'))
            ->add('YearEdition', 'text', array('label' => 'Год выпуска'))
            ->add('Elaboration', null, array('label' => 'Разработчик'))
            ->add('CompaniesDescription', null, array('label' => 'Описание компании'))
            ->add('ClPhGrDescription', null, array('label' => 'Фармакологическое группа'))
            ->add('PhInfluence', null, array('label' => 'Воздействия'))
            ->add('PhKinetics', null, array('label' => 'Фармакокинетика'))
            ->add('Dosage', null, array('label' => 'Режим дозирования'))
            ->add('OverDosage', null, array('label' => 'Передозировка'))
            ->add('Interaction', null, array('label' => 'Взаимодействие'))
            ->add('Lactation', null, array('label' => 'Лактация'))
            ->add('SideEffects', null, array('label' => 'Побочные действия'))
            ->add('StorageCondition', null, array('label' => 'Условия и сроки хранения'))
            ->add('Indication', null, array('label' => 'Показания'))
            ->add('ContraIndication', null, array('label' => 'Противопоказания'))
            ->add('SpecialInstruction', null, array('label' => 'Особые указания'))
            ->add('ShowGenericsOnlyInGNList', null, array('label' => 'В списке генериков'))
            ->add('NewForCurrentEdition', null, array('label' => 'Новое в издании'))
            ->add('CountryEditionCode', null, array('label' => 'Код страны производителя'))
            ->add('IsApproved', null, array('label' => 'Разрешения'))
            ->add('CountOfColorPhoto', null, array('label' => 'Кол-во цвет фотографий'))
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
            ->add('ArticleID')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('ArticleID')
            ->add('RusName')
            ->add('EngName')
			->add('_action', 'actions', array(
				'label'   => 'Действия',
				'actions' => array(
					'view'   => array(),
					'edit'   => array(),
					'delete' => array(),
				)
			));
        ;
    }
}