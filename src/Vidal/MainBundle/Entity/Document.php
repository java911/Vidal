<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/** @ORM\Entity(repositoryClass="DocumentRepository") @ORM\Table(name="document") */
class Document
{
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $DocumentID;

	/** @ORM\Column(length=500) */
	protected $RusName;

	/** @ORM\Column(length=500) */
	protected $EngName;

	/** @ORM\Column(length=500, nullable=true) */
	protected $Name;

	/** @ORM\Column(type="text", nullable=true) */
	protected $CompiledComposition;

	/** @ORM\Column(type="integer") */
	protected $ArticleID;

	/** @ORM\Column(length=4) */
	protected $YearEdition;

	/** @ORM\Column(type="datetime") @Gedmo\Timestampable(on="update") */
	protected $DateOfIncludingText;

	/** @ORM\Column(type="datetime") */
	protected $DateTextModified;

	/** @ORM\Column(length=255) */
	protected $Elaboration;

	/** @ORM\Column(type="text") */
	protected $CompaniesDescription;

	/** @ORM\Column(type="text", nullable=true) */
	protected $ClPhGrDescription;

	/** @ORM\Column(type="text", nullable=true) */
	protected $PhInfluence;

	/** @ORM\Column(type="text", nullable=true) */
	protected $PhKinetics;

	/** @ORM\Column(type="text", nullable=true) */
	protected $Dosage;

	/** @ORM\Column(type="text", nullable=true) */
	protected $OverDosage;

	/** @ORM\Column(type="text", nullable=true) */
	protected $Interaction;

	/** @ORM\Column(type="text", nullable=true) */
	protected $Lactation;

	/** @ORM\Column(type="text", nullable=true) */
	protected $SideEffects;

	/** @ORM\Column(type="text", nullable=true) */
	protected $StorageCondition;

	/** @ORM\Column(type="text", nullable=true) */
	protected $Indication;

	/** @ORM\Column(type="text", nullable=true) */
	protected $ContraIndication;

	/** @ORM\Column(type="text", nullable=true) */
	protected $SpecialInstruction;

	/** @ORM\Column(type="boolean") */
	protected $ShowGenericsOnlyInGNList = false;

	/** @ORM\Column(type="boolean") */
	protected $NewForCurrentEdition = false;

	/** @ORM\Column(length=10) */
	protected $CountryEditionCode;

	/** @ORM\Column(type="boolean") */
	protected $IsApproved = false;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $CountOfColorPhoto;

	/** @ORM\Column(length=4, nullable=true) */
	protected $PregnancyUsing;

	/** @ORM\Column(length=4, nullable=true) */
	protected $NursingUsing;

	/** @ORM\Column(type="text", nullable=true) */
	protected $RenalInsuf;

	/** @ORM\Column(length=4, nullable=true) */
	protected $RenalInsufUsing;

	/** @ORM\Column(type="text", nullable=true) */
	protected $HepatoInsuf;

	/** @ORM\Column(length=4, nullable=true) */
	protected $HepatoInsufUsing;

	/** @ORM\Column(type="text", nullable=true) */
	protected $PharmDelivery;

	/** @ORM\Column(type="boolean") */
	protected $WithoutRenalInsuf = false;

	/** @ORM\Column(type="boolean") */
	protected $WithoutHepatoInsuf = false;

	/** @ORM\Column(type="text", nullable=true) */
	protected $ElderlyInsuf;

	/** @ORM\Column(length=4, nullable=true) */
	protected $ElderlyInsufUsing;

	/** @ORM\Column(type="text", nullable=true) */
	protected $ChildInsuf;

	/** @ORM\Column(length=4, nullable=true) */
	protected $ChildInsufUsing;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $ed;

	/**
	 * @ORM\ManyToMany(targetEntity="ATC", mappedBy="documents")
	 * @ORM\JoinTable(name="documentoc_atc",
	 *        joinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="ATCCode", referencedColumnName="ATCCode")})
	 */
	protected $atcCodes;

	/** @ORM\OneToMany(targetEntity="ProductDocument", mappedBy="DocumentID") */
	protected $productDocument;

	/**
	 * @ORM\ManyToMany(targetEntity="Nozology", mappedBy="documents")
	 * @ORM\JoinTable(name="document_indicnozology",
	 *        joinColumns={@ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="NozologyCode", referencedColumnName="NozologyCode")})
	 */
	protected $nozologies;

	/**
	 * @ORM\ManyToMany(targetEntity="ClinicoPhPointers", mappedBy="documents")
	 * @ORM\JoinTable(name="document_clphpointers",
	 *        joinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="ClPhPointerID", referencedColumnName="ClPhPointerID")})
	 */
	protected $clphPointers;

	/**
	 * @ORM\ManyToMany(targetEntity="Contraindication", mappedBy="documents")
	 * @ORM\JoinTable(name="document_contraindication",
	 * joinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")},
	 * inverseJoinColumns={@ORM\JoinColumn(name="ContraIndicCode", referencedColumnName="ContraIndicCode")})
	 */
	protected $contraindications;

	/** @ORM\OneToMany(targetEntity="DocumentInfoPage", mappedBy="DocumentID") */
	protected $documentInfoPages;

	/**
	 * @ORM\ManyToMany(targetEntity="Edition", mappedBy="documents")
	 * @ORM\JoinTable(name="documentoc_edition",
	 *        joinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="EditionCode", referencedColumnName="EditionCode")})
	 */
	protected $editions;

	/** @ORM\OneToMany(targetEntity="DocumentEdition", mappedBy="DocumentID") */
	protected $documentEditions;

	/** @ORM\OneToMany(targetEntity="MoleculeDocument", mappedBy="DocumentID") */
	protected $moleculeDocuments;

	public function __construct()
	{
		$this->atcCodes          = new ArrayCollection();
		$this->productDocument   = new ArrayCollection();
		$this->nozologies        = new ArrayCollection();
		$this->clphPointers      = new ArrayCollection();
		$this->contraindications = new ArrayCollection();
		$this->documentInfoPages = new ArrayCollection();
		$this->documentEditions  = new ArrayCollection();
		$this->moleculeDocuments = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->RusName;
	}

	/**
	 * @param mixed $ArticleID
	 */
	public function setArticleID($ArticleID)
	{
		$this->ArticleID = $ArticleID;
	}

	/**
	 * @return mixed
	 */
	public function getArticleID()
	{
		return $this->ArticleID;
	}

	/**
	 * @param mixed $ChildInsuf
	 */
	public function setChildInsuf($ChildInsuf)
	{
		$this->ChildInsuf = $ChildInsuf;
	}

	/**
	 * @return mixed
	 */
	public function getChildInsuf()
	{
		return $this->ChildInsuf;
	}

	/**
	 * @param mixed $ChildInsufUsing
	 */
	public function setChildInsufUsing($ChildInsufUsing)
	{
		$this->ChildInsufUsing = $ChildInsufUsing;
	}

	/**
	 * @return mixed
	 */
	public function getChildInsufUsing()
	{
		return $this->ChildInsufUsing;
	}

	/**
	 * @param mixed $ClPhGrDescription
	 */
	public function setClPhGrDescription($ClPhGrDescription)
	{
		$this->ClPhGrDescription = $ClPhGrDescription;
	}

	/**
	 * @return mixed
	 */
	public function getClPhGrDescription()
	{
		return $this->ClPhGrDescription;
	}

	/**
	 * @param mixed $CompaniesDescription
	 */
	public function setCompaniesDescription($CompaniesDescription)
	{
		$this->CompaniesDescription = $CompaniesDescription;
	}

	/**
	 * @return mixed
	 */
	public function getCompaniesDescription()
	{
		return $this->CompaniesDescription;
	}

	/**
	 * @param mixed $CompiledComposition
	 */
	public function setCompiledComposition($CompiledComposition)
	{
		$this->CompiledComposition = $CompiledComposition;
	}

	/**
	 * @return mixed
	 */
	public function getCompiledComposition()
	{
		return $this->CompiledComposition;
	}

	/**
	 * @param mixed $ContraIndication
	 */
	public function setContraIndication($ContraIndication)
	{
		$this->ContraIndication = $ContraIndication;
	}

	/**
	 * @return mixed
	 */
	public function getContraIndication()
	{
		return $this->ContraIndication;
	}

	/**
	 * @param mixed $CountOfColorPhoto
	 */
	public function setCountOfColorPhoto($CountOfColorPhoto)
	{
		$this->CountOfColorPhoto = $CountOfColorPhoto;
	}

	/**
	 * @return mixed
	 */
	public function getCountOfColorPhoto()
	{
		return $this->CountOfColorPhoto;
	}

	/**
	 * @param mixed $CountryEditionCode
	 */
	public function setCountryEditionCode($CountryEditionCode)
	{
		$this->CountryEditionCode = $CountryEditionCode;
	}

	/**
	 * @return mixed
	 */
	public function getCountryEditionCode()
	{
		return $this->CountryEditionCode;
	}

	/**
	 * @param mixed $DateOfIncludingText
	 */
	public function setDateOfIncludingText($DateOfIncludingText)
	{
		$this->DateOfIncludingText = $DateOfIncludingText;
	}

	/**
	 * @return mixed
	 */
	public function getDateOfIncludingText()
	{
		return $this->DateOfIncludingText;
	}

	/**
	 * @param mixed $DateTextModified
	 */
	public function setDateTextModified($DateTextModified)
	{
		$this->DateTextModified = $DateTextModified;
	}

	/**
	 * @return mixed
	 */
	public function getDateTextModified()
	{
		return $this->DateTextModified;
	}

	/**
	 * @param mixed $DocumentID
	 */
	public function setDocumentID($DocumentID)
	{
		$this->DocumentID = $DocumentID;
	}

	/**
	 * @return mixed
	 */
	public function getDocumentID()
	{
		return $this->DocumentID;
	}

	/**
	 * @param mixed $Dosage
	 */
	public function setDosage($Dosage)
	{
		$this->Dosage = $Dosage;
	}

	/**
	 * @return mixed
	 */
	public function getDosage()
	{
		return $this->Dosage;
	}

	/**
	 * @param mixed $Elaboration
	 */
	public function setElaboration($Elaboration)
	{
		$this->Elaboration = $Elaboration;
	}

	/**
	 * @return mixed
	 */
	public function getElaboration()
	{
		return $this->Elaboration;
	}

	/**
	 * @param mixed $ElderlyInsuf
	 */
	public function setElderlyInsuf($ElderlyInsuf)
	{
		$this->ElderlyInsuf = $ElderlyInsuf;
	}

	/**
	 * @return mixed
	 */
	public function getElderlyInsuf()
	{
		return $this->ElderlyInsuf;
	}

	/**
	 * @param mixed $ElderlyInsufUsing
	 */
	public function setElderlyInsufUsing($ElderlyInsufUsing)
	{
		$this->ElderlyInsufUsing = $ElderlyInsufUsing;
	}

	/**
	 * @return mixed
	 */
	public function getElderlyInsufUsing()
	{
		return $this->ElderlyInsufUsing;
	}

	/**
	 * @param mixed $EngName
	 */
	public function setEngName($EngName)
	{
		$this->EngName = $EngName;
	}

	/**
	 * @return mixed
	 */
	public function getEngName()
	{
		return $this->EngName;
	}

	/**
	 * @param mixed $HepatoInsuf
	 */
	public function setHepatoInsuf($HepatoInsuf)
	{
		$this->HepatoInsuf = $HepatoInsuf;
	}

	/**
	 * @return mixed
	 */
	public function getHepatoInsuf()
	{
		return $this->HepatoInsuf;
	}

	/**
	 * @param mixed $HepatoInsufUsing
	 */
	public function setHepatoInsufUsing($HepatoInsufUsing)
	{
		$this->HepatoInsufUsing = $HepatoInsufUsing;
	}

	/**
	 * @return mixed
	 */
	public function getHepatoInsufUsing()
	{
		return $this->HepatoInsufUsing;
	}

	/**
	 * @param mixed $Indication
	 */
	public function setIndication($Indication)
	{
		$this->Indication = $Indication;
	}

	/**
	 * @return mixed
	 */
	public function getIndication()
	{
		return $this->Indication;
	}

	/**
	 * @param mixed $Interaction
	 */
	public function setInteraction($Interaction)
	{
		$this->Interaction = $Interaction;
	}

	/**
	 * @return mixed
	 */
	public function getInteraction()
	{
		return $this->Interaction;
	}

	/**
	 * @param mixed $IsApproved
	 */
	public function setIsApproved($IsApproved)
	{
		$this->IsApproved = $IsApproved;
	}

	/**
	 * @return mixed
	 */
	public function getIsApproved()
	{
		return $this->IsApproved;
	}

	/**
	 * @param mixed $Lactation
	 */
	public function setLactation($Lactation)
	{
		$this->Lactation = $Lactation;
	}

	/**
	 * @return mixed
	 */
	public function getLactation()
	{
		return $this->Lactation;
	}

	/**
	 * @param mixed $NewForCurrentEdition
	 */
	public function setNewForCurrentEdition($NewForCurrentEdition)
	{
		$this->NewForCurrentEdition = $NewForCurrentEdition;
	}

	/**
	 * @return mixed
	 */
	public function getNewForCurrentEdition()
	{
		return $this->NewForCurrentEdition;
	}

	/**
	 * @param mixed $OverDosage
	 */
	public function setOverDosage($OverDosage)
	{
		$this->OverDosage = $OverDosage;
	}

	/**
	 * @return mixed
	 */
	public function getOverDosage()
	{
		return $this->OverDosage;
	}

	/**
	 * @param mixed $PhInfluence
	 */
	public function setPhInfluence($PhInfluence)
	{
		$this->PhInfluence = $PhInfluence;
	}

	/**
	 * @return mixed
	 */
	public function getPhInfluence()
	{
		return $this->PhInfluence;
	}

	/**
	 * @param mixed $PhKinetics
	 */
	public function setPhKinetics($PhKinetics)
	{
		$this->PhKinetics = $PhKinetics;
	}

	/**
	 * @return mixed
	 */
	public function getPhKinetics()
	{
		return $this->PhKinetics;
	}

	/**
	 * @param mixed $PharmDelivery
	 */
	public function setPharmDelivery($PharmDelivery)
	{
		$this->PharmDelivery = $PharmDelivery;
	}

	/**
	 * @return mixed
	 */
	public function getPharmDelivery()
	{
		return $this->PharmDelivery;
	}

	/**
	 * @param mixed $PregnancyUsing
	 */
	public function setPregnancyUsing($PregnancyUsing)
	{
		$this->PregnancyUsing = $PregnancyUsing;
	}

	/**
	 * @return mixed
	 */
	public function getPregnancyUsing()
	{
		return $this->PregnancyUsing;
	}

	/**
	 * @param mixed $RenalInsuf
	 */
	public function setRenalInsuf($RenalInsuf)
	{
		$this->RenalInsuf = $RenalInsuf;
	}

	/**
	 * @return mixed
	 */
	public function getRenalInsuf()
	{
		return $this->RenalInsuf;
	}

	/**
	 * @param mixed $RenalInsufUsing
	 */
	public function setRenalInsufUsing($RenalInsufUsing)
	{
		$this->RenalInsufUsing = $RenalInsufUsing;
	}

	/**
	 * @return mixed
	 */
	public function getRenalInsufUsing()
	{
		return $this->RenalInsufUsing;
	}

	/**
	 * @param mixed $RusName
	 */
	public function setRusName($RusName)
	{
		$this->RusName = $RusName;
	}

	/**
	 * @return mixed
	 */
	public function getRusName()
	{
		return $this->RusName;
	}

	/**
	 * @param mixed $ShowGenericsOnlyInGNList
	 */
	public function setShowGenericsOnlyInGNList($ShowGenericsOnlyInGNList)
	{
		$this->ShowGenericsOnlyInGNList = $ShowGenericsOnlyInGNList;
	}

	/**
	 * @return mixed
	 */
	public function getShowGenericsOnlyInGNList()
	{
		return $this->ShowGenericsOnlyInGNList;
	}

	/**
	 * @param mixed $SideEffects
	 */
	public function setSideEffects($SideEffects)
	{
		$this->SideEffects = $SideEffects;
	}

	/**
	 * @return mixed
	 */
	public function getSideEffects()
	{
		return $this->SideEffects;
	}

	/**
	 * @param mixed $SpecialInstruction
	 */
	public function setSpecialInstruction($SpecialInstruction)
	{
		$this->SpecialInstruction = $SpecialInstruction;
	}

	/**
	 * @return mixed
	 */
	public function getSpecialInstruction()
	{
		return $this->SpecialInstruction;
	}

	/**
	 * @param mixed $StorageCondition
	 */
	public function setStorageCondition($StorageCondition)
	{
		$this->StorageCondition = $StorageCondition;
	}

	/**
	 * @return mixed
	 */
	public function getStorageCondition()
	{
		return $this->StorageCondition;
	}

	/**
	 * @param mixed $WithoutHepatoInsuf
	 */
	public function setWithoutHepatoInsuf($WithoutHepatoInsuf)
	{
		$this->WithoutHepatoInsuf = $WithoutHepatoInsuf;
	}

	/**
	 * @return mixed
	 */
	public function getWithoutHepatoInsuf()
	{
		return $this->WithoutHepatoInsuf;
	}

	/**
	 * @param mixed $WithoutRenalInsuf
	 */
	public function setWithoutRenalInsuf($WithoutRenalInsuf)
	{
		$this->WithoutRenalInsuf = $WithoutRenalInsuf;
	}

	/**
	 * @return mixed
	 */
	public function getWithoutRenalInsuf()
	{
		return $this->WithoutRenalInsuf;
	}

	/**
	 * @param mixed $YearEdition
	 */
	public function setYearEdition($YearEdition)
	{
		$this->YearEdition = $YearEdition;
	}

	/**
	 * @return mixed
	 */
	public function getYearEdition()
	{
		return $this->YearEdition;
	}

	/**
	 * @param mixed $ed
	 */
	public function setEd($ed)
	{
		$this->ed = $ed;
	}

	/**
	 * @return mixed
	 */
	public function getEd()
	{
		return $this->ed;
	}

	/**
	 * @param mixed $atcCodes
	 */
	public function setAtcCodes(ArrayCollection $atcCodes)
	{
		$this->atcCodes = $atcCodes;
	}

	/**
	 * @return mixed
	 */
	public function getAtcCodes()
	{
		return $this->atcCodes;
	}

	/**
	 * @param mixed $nozologies
	 */
	public function setNozologies(ArrayCollection $nozologies)
	{
		$this->nozologies = $nozologies;
	}

	/**
	 * @return mixed
	 */
	public function getNozologies()
	{
		return $this->nozologies;
	}

	/**
	 * @param mixed $productDocument
	 */
	public function setProductDocument(ArrayCollection $productDocument)
	{
		$this->productDocument = $productDocument;
	}

	/**
	 * @return mixed
	 */
	public function getProductDocument()
	{
		return $this->productDocument;
	}

	/**
	 * @param mixed $clphPointers
	 */
	public function setClphPointers(ArrayCollection $clphPointers)
	{
		$this->clphPointers = $clphPointers;
	}

	/**
	 * @return mixed
	 */
	public function getClphPointers()
	{
		return $this->clphPointers;
	}

	/**
	 * @param mixed $contraindications
	 */
	public function setContraindications(ArrayCollection $contraindications)
	{
		$this->contraindications = $contraindications;
	}

	/**
	 * @return mixed
	 */
	public function getContraindications()
	{
		return $this->contraindications;
	}

	/**
	 * @param mixed $documentInfoPages
	 */
	public function setDocumentInfoPages(ArrayCollection $documentInfoPages)
	{
		$this->documentInfoPages = $documentInfoPages;
	}

	/**
	 * @return mixed
	 */
	public function getDocumentInfoPages()
	{
		return $this->documentInfoPages;
	}

	/**
	 * @param mixed $documentEditions
	 */
	public function setDocumentEditions(ArrayCollection $documentEditions)
	{
		$this->documentEditions = $documentEditions;
	}

	/**
	 * @return mixed
	 */
	public function getDocumentEditions()
	{
		return $this->documentEditions;
	}

	/**
	 * @param mixed $editions
	 */
	public function setEditions(ArrayCollection $editions)
	{
		$this->editions = $editions;
	}

	/**
	 * @return mixed
	 */
	public function getEditions()
	{
		return $this->editions;
	}

	/**
	 * @param mixed $moleculeDocuments
	 */
	public function setMoleculeDocuments(ArrayCollection $moleculeDocuments)
	{
		$this->moleculeDocuments = $moleculeDocuments;
	}

	/**
	 * @return mixed
	 */
	public function getMoleculeDocuments()
	{
		return $this->moleculeDocuments;
	}

	/**
	 * @param mixed $NursingUsing
	 */
	public function setNursingUsing($NursingUsing)
	{
		$this->NursingUsing = $NursingUsing;
	}

	/**
	 * @return mixed
	 */
	public function getNursingUsing()
	{
		return $this->NursingUsing;
	}

	/**
	 * @param mixed $Name
	 */
	public function setName($Name)
	{
		$this->Name = $Name;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->Name;
	}
}