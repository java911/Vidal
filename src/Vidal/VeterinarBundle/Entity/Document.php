<?php
namespace Vidal\VeterinarBundle\Entity;

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
	protected $ClPhGrName;

	/** @ORM\Column(type="text", nullable=true) */
	protected $PhInfluence;

	/** @ORM\Column(type="text", nullable=true) */
	protected $Dosage;

	/** @ORM\Column(type="text", nullable=true) */
	protected $SideEffects;

	/** @ORM\Column(type="text", nullable=true) */
	protected $StorageCondition;

	/** @ORM\Column(type="text", nullable=true) */
	protected $Indication;

	/** @ORM\Column(type="text", nullable=true) */
	protected $SpecialInstruction;

	/** @ORM\Column(type="text", nullable=true) */
	protected $ContraIndication;

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

	/** @ORM\Column(type="text", nullable=true) */
	protected $PharmDelivery;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $ed;

	/** @ORM\OneToMany(targetEntity="ProductDocument", mappedBy="DocumentID") */
	protected $productDocument;

	/**
	 * @ORM\ManyToMany(targetEntity="ClinicoPhPointers", mappedBy="documents")
	 * @ORM\JoinTable(name="document_clphpointers",
	 *        joinColumns={@ORM\JoinColumn(name="DocumentID", referencedColumnName="DocumentID")},
	 *        inverseJoinColumns={@ORM\JoinColumn(name="ClPhPointerID", referencedColumnName="ClPhPointerID")})
	 */
	protected $clphPointers;

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
		$this->productDocument   = new ArrayCollection();
		$this->clphPointers      = new ArrayCollection();
		$this->documentInfoPages = new ArrayCollection();
		$this->documentEditions  = new ArrayCollection();
		$this->moleculeDocuments = new ArrayCollection();
	}

	public function __toString()
	{
		return empty($this->RusName) ? '' : $this->RusName;
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

	/**
	 * @param mixed $ClPhGrName
	 */
	public function setClPhGrName($ClPhGrName)
	{
		$this->ClPhGrName = $ClPhGrName;
	}

	/**
	 * @return mixed
	 */
	public function getClPhGrName()
	{
		return $this->ClPhGrName;
	}
}