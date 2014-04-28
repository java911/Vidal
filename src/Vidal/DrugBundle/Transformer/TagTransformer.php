<?php

namespace Vidal\DrugBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Vidal\DrugBundle\Entity\ArtTag;

class ArtTagTransformer implements DataTransformerInterface
{
	private $om;

	private $subject;

	public function __construct(ObjectManager $om, $subject)
	{
		$this->om      = $om;
		$this->subject = $subject;
	}

	public function transform($searchTags)
	{
		return '';
	}

	public function reverseTransform($text)
	{
		$text = trim($text);

		if (empty($text)) {
			return null;
		}

		$tag  = $this->om->getRepository('VidalDrugBundle:ArtTag')->findOneByText($text);

		if (empty($tag)) {
			$tag = new ArtTag();
			$tag->setText($text);
			$this->om->persist($tag);
			$this->om->flush($tag);
			$this->om->refresh($tag);
		}

		$this->subject->addTag($tag);

		return null;
	}
}