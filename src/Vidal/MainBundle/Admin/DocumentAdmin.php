<?php
// src/Vidal/mainBundle/Admin/PostAdmin.php

namespace Vidal\MainBundle\Admin;

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
            ->add('EngName', 'text', array('label' => 'Зарубежное название'))
            ->add('CompiledComposition', 'textarea', array('label' => 'Форма выпуска / состав и упаковка'))
            ->add('ArticleID', 'text', array('label' => 'Артикуль'))
            ->add('YearEdition', 'text', array('label' => 'YearEdition'))
            ->add('DateOfIncludingText')
            ->add('DateTextModified')
            ->add('Elaboration')
            ->add('CompaniesDescription')
            ->add('ClPhGrDescription')
            ->add('PhInfluence')
            ->add('PhKinetics')
            ->add('Dosage')
            ->add('OverDosage')
            ->add('Interaction')
            ->add('Lactation')
            ->add('SideEffects')
            ->add('StorageCondition')
            ->add('Indication')
            ->add('ContraIndication')
            ->add('SpecialInstruction')
            ->add('ShowGenericsOnlyInGNList')
            ->add('NewForCurrentEdition')
            ->add('CountryEditionCode')
            ->add('IsApproved')
            ->add('CountOfColorPhoto')
            ->add('PregnancyUsing')
            ->add('NursingUsing')
            ->add('RenalInsuf')
            ->add('RenalInsufUsing')
            ->add('HepatoInsuf')
            ->add('HepatoInsufUsing')
            ->add('PharmDelivery')
            ->add('WithoutRenalInsuf')
            ->add('WithoutHepatoInsuf')
            ->add('ElderlyInsuf')
            ->add('ElderlyInsufUsing')
            ->add('ChildInsuf')
            ->add('ChildInsufUsing')
            ->add('ed')

//            ->add('atcCodes', 'entity', array('class' => 'Vidal\mainBundle\Entity\ATC'))


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
        ;
    }
}