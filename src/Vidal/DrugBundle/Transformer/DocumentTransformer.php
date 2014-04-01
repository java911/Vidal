<?php

namespace Vidal\DrugBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Vidal\DrugBundle\Entity\Document;

class DocumentTransformer implements DataTransformerInterface
{
	private $em;

	public function __construct(ObjectManager $em)
	{
		$this->em = $em;
	}

	public function transform($document)
	{
		return $document ? $document->__toString() : '';
	}

	public function reverseTransform($text)
	{
		return $text ? $this->em->getRepository('VidalDrugBundle:Document')->findOneByText($text) : null;
	}
}