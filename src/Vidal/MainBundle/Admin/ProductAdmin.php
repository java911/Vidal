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
            ->add('NonPrescriptionDrug', 'text', array('label' => 'NonPrescriptionDrug'))
            ->add('CountryEditionCode')
            ->add('RegistrationDate')
            ->add('DateOfCloseRegistration')
            ->add('RegistrationNumber')
            ->add('PPR')
            ->add('ZipInfo')
            ->add('Composition')
            ->add('DateOfIncludingText')
            ->add('ProductTypeCode')
            ->add('ItsMultiProduct')
            ->add('BelongMultiProductID')
            ->add('MarketStatusID')
            ->add('CheckingRegDate')
            ->add('Personal')
            ->add('m')
            ->add('GNVLS')
            ->add('DLO')
            ->add('List_AB')
            ->add('List_PKKN')
            ->add('StrongMeans')
            ->add('Poison')
            ->add('MinAs')
            ->add('ValidPeriod')
            ->add('StrCond')


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