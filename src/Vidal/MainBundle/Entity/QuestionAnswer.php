<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity(repositoryClass="QuestionAnswerRepository") @ORM\Table(name="question_answer") */
class QuestionAnswer extends BaseEntity
{

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Пожалуйста, укажите Имя")
     */
    protected $authorFirstName;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Пожалуйста, укажите E-mail")
     */
    protected $authorEmail;

    /**
     * @ORM\ManyToOne(targetEntity="user", inversedBy="answers")
     */
    protected $answerUser;

    /**
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank(message="Пожалуйста, укажите вопрос")
	 */
	protected $question;

	/**
	 * @ORM\Column(type="text", nullable = true)
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

    /**
     * @param mixed $authorEmail
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
    }

    /**
     * @return mixed
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * @param mixed $authorFirstName
     */
    public function setAuthorFirstName($authorFirstName)
    {
        $this->authorFirstName = $authorFirstName;
    }

    /**
     * @return mixed
     */
    public function getAuthorFirstName()
    {
        return $this->authorFirstName;
    }

    /**
     * @param mixed $answerUser
     */
    public function setAnswerUser($answerUser)
    {
        $this->answerUser = $answerUser;
    }

    /**
     * @return mixed
     */
    public function getAnswerUser()
    {
        return $this->answerUser;
    }



}