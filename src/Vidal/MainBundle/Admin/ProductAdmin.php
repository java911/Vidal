<?php
// src/Vidal/mainBundle/Admin/PostAdmin.php

namespace Vidal\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class productAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('RusName', 'text', array('label' => 'Русское название'))
            ->add('EngName', 'text', array('label' => 'Зарубежное название'))
            ->add('NonPrescriptionDrug', 'text', array('label' => 'Не рециптуальный'))
            ->add('CountryEditionCode', null, array('label' => 'Страна изготовитель'))
            ->add('RegistrationDate', null, array('label' => 'Дата регистрации'))
            ->add('DateOfCloseRegistration', null, array('label' => 'Регистрация до'))
            ->add('RegistrationNumber', null, array('label' => 'Номер регистрации'))
            ->add('PPR')
            ->add('ZipInfo', null, array('label' => 'Форма выпуска'))
            ->add('Composition', null, array('label' => 'Форма выпуска'))
            ->add('ProductTypeCode',null, array('label' => 'Тип препарата'))
            ->add('ItsMultiProduct', null, array('label' => 'мультипродукт'))
            ->add('BelongMultiProductID', null, array('label' => 'Мульти продукт ID'))
            ->add('MarketStatusID', null, array('label' => 'Статус ID'))
            ->add('CheckingRegDate', null, array('label' => 'Проверка даты регистрации'))
            ->add('Personal', null, array('label' => 'персональный'))
            ->add('m')
            ->add('GNVLS')
            ->add('DLO')
            ->add('List_AB', null, array('label' => 'Список A.B.'))
            ->add('List_PKKN', null, array('label' => 'Список P.K.K.N') )
            ->add('StrongMeans', null, array('label' => 'Сильнодействующий'))
            ->add('Poison', null, array('label' => 'отрава'))
            ->add('MinAs', null, array('label' => 'Минимум'))
            ->add('ValidPeriod', null, array('label' => 'Период действия'))
            ->add('StrCond', null, array('label' => 'StrCond'))
            ->add('DateOfIncludingText', null, array('label' => 'дата создания страницы'))


//            ->add('atcCodes')
//            ->add('productDocument')
//            ->add('nozologies')
//            ->add('clphPointers')
//            ->add('documentEditions')

//            ->add('atcCodes', 'entity', array('class' => 'Vidal\mainBundle\Entity\documentoc_atc'))
//            ->add('productDocument', 'entity', array('class' => 'Vidal\mainBundle\Entity\ProductDocument'))
//            ->add('nozologies', 'entity', array('class' => 'Vidal\mainBundle\Entity\document_indicnozology'))
//            ->add('clphPointers', 'entity', array('class' => 'Vidal\mainBundle\Entity\NozologyCode'))
//            ->add('documentEditions', 'entity', array('class' => 'Vidal\mainBundle\Entity\DocumentEdition'))
//            ->add('moleculeDocuments', 'entity', array('class' => 'Vidal\mainBundle\Entity\MoleculeDocument'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('RusName')
            ->add('EngName')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('ProductID')
            ->add('RusName')
            ->add('EngName')
        ;
    }
}