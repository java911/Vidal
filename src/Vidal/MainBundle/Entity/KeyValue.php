<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity(repositoryClass="KeyValueRepository") @ORM\Table(name="key_value") */
class KeyValue
{
	/** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue */
    protected $id;

	/** @ORM\Column(type="string", length=255) */
	protected $key;

	/** @ORM\Column(type="string", length=255) */
	protected $value;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $key
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	/**
	 * @return mixed
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}
}
