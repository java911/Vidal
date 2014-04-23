<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity()
 * @ORM\Table()
 */
class AstrazenecaFaq extends BaseEntity
{
	/**
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank(message="Пожалуйста, укажите вопрос")
	 */
	protected $question;

	/**
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank(message="Пожалуйста, укажите ответ")
	 */
	protected $answer;

	public function __construct()
	{
		$now           = new \DateTime('now');
		$this->created = $now;
		$this->updated = $now;
	}

	public function __toString()
	{
		return $this->question;
	}

	/**
	 * @param mixed $answer
	 */
	public function setAnswer($answer)
	{
		$this->answer = $answer;
	}

	/**
	 * @return mixed
	 */
	public function getAnswer()
	{
		return $this->answer;
	}

	/**
	 * @param mixed $question
	 */
	public function setQuestion($question)
	{
		$this->question = $question;
	}

	/**
	 * @return mixed
	 */
	public function getQuestion()
	{
		return $this->question;
	}
}