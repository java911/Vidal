<?php

namespace Vidal\DrugBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Vidal\DrugBundle\Entity\Document;
use Vidal\DrugBundle\Entity\Publication;
use Vidal\DrugBundle\Entity\Art;
use Vidal\DrugBundle\Entity\Article;

class DocumentTransformer implements DataTransformerInterface
{
	private $em;
	private $object;

	public function __construct(ObjectManager $em, $object)
	{
		$this->em     = $em;
		$this->object = $object;
	}

	public function transform($documents)
	{
		return '';

//		if (count($documents) < 1) {
//			return '';
//		}
//
//		$documentIds = array();
//
//		foreach ($documents as $document) {
//			$documentIds[] = $document->getDocumentID();
//		}
//
//		return implode(';', $documentIds);
	}

	public function reverseTransform($text)
	{
		$document = $this->em->getRepository('VidalDrugBundle:Document')->findOneByText($text);

		if ($document) {
			if ($this->object->getDocuments()->contains($document)) {
				throw new TransformationFailedException('Такое описание препарата уже было добавлено');
			}
			else {
				$this->object->addDocument($document);
				$this->em->flush();
			}
		}
		else {
			throw new TransformationFailedException('Описание препарата не найдено');
		}

		return $this->object->getDocuments();
	}
}