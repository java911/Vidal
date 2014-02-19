<?php
namespace Vidal\MainBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity(repositoryClass="UserRepository") @ORM\Table(name="user") */
class User extends BaseEntity implements UserInterface, EquatableInterface, \Serializable
{
	/**
	 * @ORM\Column(type="string", unique = true)
	 * @Assert\NotBlank(message = "Введите e-mail")
	 * @Assert\Email(message = "Некорректный e-mail")
	 */
	protected $username;

	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(message = "Придумайте себе пароль")
	 */
	protected $password;

	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(message="Укажите свое имя")
	 */
	protected $firstName;

	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(message="Укажите свою фамилию")
	 */
	protected $lastName;

	/** @ORM\Column(type="string", nullable=true) */
	protected $surName;

	/** @ORM\Column(type="string", nullable=true) */
	protected $hash;

	/** @ORM\Column(type="string", nullable=true) */
	protected $salt;

	/** @ORM\Column(type="datetime", nullable=true) */
	protected $lastLogin;

	/** @ORM\Column(type="boolean", nullable=false) */
	protected $emailConfirmed;

	/** @ORM\Column(type="string") */
	protected $roles;

	/** @ORM\ManyToOne(targetEntity="Specialization", inversedBy="doctors") */
	protected $specialization;

	/** @ORM\ManyToOne(targetEntity="Specialty", inversedBy="primaryDoctors") */
	protected $primarySpecialty;

	/** @ORM\ManyToOne(targetEntity="Specialty", inversedBy="secondaryDoctors") */
	protected $secondarySpecialty;

	/** @ORM\ManyToOne(targetEntity="City", inversedBy="doctors") */
	protected $city;

	/** @ORM\Column(type="boolean") */
	protected $student;

	/** @ORM\ManyToOne(targetEntity="University", inversedBy="doctors") */
	protected $university;

	/**
	 * @ORM\Column(type="date")
	 * @Assert\NotBlank(message = "Укажите год окончания ВУЗа")
	 */
	protected $graduateYear;

	/**
	 * @ORM\Column(type="date")
	 * @Assert\NotBlank(message="Укажите дату своего рождения")
	 * @Assert\DateTime(message="Дата указана в неверно")
	 */
	protected $birthdate;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @Assert\NotBlank(message="Укажите ученую степень")
	 * @Assert\Choice(callback="getAcademicDegrees", message="Некорректная ученая степень. Пожалуйста, выберите из списка.")
	 */
	protected $academicDegree;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @Assert\NotBlank(message="Укажите ваше место работы")
	 * @Assert\Choice(callback="getJobTypes", message="Некорректное место работы. Пожалуйста, выберите из списка.")
	 */
	protected $jobType;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @Assert\NotBlank(message="Укажите вид организации, где вы работаете")
	 * @Assert\Choice(callback="getJobAlignments", message="Некорректный вид организации. Пожалуйста, выберите из списка")
	 */
	protected $jobAlignment;

	public function __construct()
	{
		$this->emailConfirmed = false;
		$this->student        = false;
		$this->roles          = 'ROLE_DOCTOR';
	}

	public function __toString()
	{
		return $this->lastName . ' '
		. mb_substr($this->firstName, 0, 1, 'utf-8') . '.'
		. ($this->surName ? ' ' . mb_substr($this->surName, 0, 1, 'utf-8') . '.' : '');
	}

	public function getPoliteReference()
	{
		return $this->firstName . ($this->surName ? ' ' . $this->surName : '');
	}

	public static function getAcademicDegrees()
	{
		return array('Нет' => 'Нет', 'Кандидат наук' => 'Кандидат наук', 'Доктор медицинских наук' => 'Доктор медицинских наук');
	}

	public static function getJobTypes()
	{
		return array('Поликлиника' => 'Поликлиника', 'Больница' => 'Больница', 'НИИ' => 'НИИ', 'Другое' => 'Другое');
	}

	public static function getJobAlignments()
	{
		return array('Государственная' => 'Государственная', 'Частная' => 'Частная');
	}

	/** @inheritDoc */
	public function getUsername()
	{
		return $this->username;
	}

	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	public function setSalt($salt)
	{
		$this->salt = $salt;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getPassword()
	{
		return $this->password;
	}

	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getRoles()
	{
		return explode(';', $this->roles);
	}

	/**
	 * Установить роли для пользователя
	 *
	 * @param array
	 * @return User
	 */
	public function setRoles($roles)
	{
		if (is_array($roles)) {
			$roles = implode($roles, ';');
		}

		$this->roles = $roles;

		return $this;
	}

	public function addRole($role)
	{
		$roles = explode(';', $this->roles);

		if (array_search($role, $roles) === false) {
			$this->roles .= ';' . $role;
		}

		return $this;
	}

	public function removeRole($role)
	{
		$roles = explode(';', $this->roles);
		$key   = array_search($role, $roles);

		if ($key !== false) {
			unset($roles[$key]);
			$this->roles = implode($roles, ';');
		}
	}

	public function checkRole($role)
	{
		$roles = explode(';', $this->roles);

		return in_array($role, $roles);
	}

	/**
	 * @inheritDoc
	 */
	public function eraseCredentials()
	{
		$this->password = null;
	}

	public function isEqualTo(UserInterface $user)
	{
		return $this->id === $user->getId();
	}

	/**
	 * Сериализуем только id, потому что UserProvider сам перезагружает остальные свойства пользователя по его id
	 *
	 * @see \Serializable::serialize()
	 */
	public function serialize()
	{
		return serialize(array(
			$this->id
		));
	}

	/**
	 * @see \Serializable::unserialize()
	 */
	public function unserialize($serialized)
	{
		list (
			$this->id
			) = unserialize($serialized);
	}

	public function setHash($hash)
	{
		$this->hash = $hash;

		return $this;
	}

	public function getHash()
	{
		return $this->hash;
	}

	public function getFirstName()
	{
		return $this->firstName;
	}

	public function setFirstName($firstName)
	{
		$this->firstName = $this->mb_ucfirst($firstName);

		return $this;
	}

	public function setLastName($lastName)
	{
		$this->lastName = $this->mb_ucfirst($lastName);

		return $this;
	}

	public function getLastName()
	{
		return $this->lastName;
	}

	public function setSurName($surName)
	{
		$this->surName = $this->mb_ucfirst($surName);

		return $this;
	}

	public function getSurName()
	{
		return $this->surName;
	}

	public function getLastLogin()
	{
		return $this->lastLogin;
	}

	public function setLastLogin(\DateTime $lastLogin)
	{
		$this->lastLogin = $lastLogin;

		return $this;
	}

	private function mb_ucfirst($string, $encoding = 'utf-8')
	{
		$strlen    = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then      = mb_substr($string, 1, $strlen - 1, $encoding);

		return mb_strtoupper($firstChar, $encoding) . $then;
	}

	/**
	 * @param mixed $emailConfirmed
	 */
	public function setEmailConfirmed($emailConfirmed)
	{
		$this->emailConfirmed = $emailConfirmed;
	}

	/**
	 * @return mixed
	 */
	public function getEmailConfirmed()
	{
		return $this->emailConfirmed;
	}

	/**
	 * @param mixed $city
	 */
	public function setCity($city)
	{
		$this->city = $city;
	}

	/**
	 * @return mixed
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * @param mixed $primarySpecialty
	 */
	public function setPrimarySpecialty($primarySpecialty)
	{
		$this->primarySpecialty = $primarySpecialty;
	}

	/**
	 * @return mixed
	 */
	public function getPrimarySpecialty()
	{
		return $this->primarySpecialty;
	}

	/**
	 * @param mixed $secondarySpecialty
	 */
	public function setSecondarySpecialty($secondarySpecialty)
	{
		$this->secondarySpecialty = $secondarySpecialty;
	}

	/**
	 * @return mixed
	 */
	public function getSecondarySpecialty()
	{
		return $this->secondarySpecialty;
	}

	/**
	 * @param mixed $student
	 */
	public function setStudent($student)
	{
		$this->student = $student;
	}

	/**
	 * @return mixed
	 */
	public function getStudent()
	{
		return $this->student;
	}

	/**
	 * @param mixed $university
	 */
	public function setUniversity($university)
	{
		$this->university = $university;
	}

	/**
	 * @return mixed
	 */
	public function getUniversity()
	{
		return $this->university;
	}

	/**
	 * @param mixed $academicDegree
	 */
	public function setAcademicDegree($academicDegree)
	{
		$this->academicDegree = $academicDegree;
	}

	/**
	 * @return mixed
	 */
	public function getAcademicDegree()
	{
		return $this->academicDegree;
	}

	/**
	 * @param mixed $graduateYear
	 */
	public function setGraduateYear($graduateYear)
	{
		$this->graduateYear = $graduateYear;
	}

	/**
	 * @return mixed
	 */
	public function getGraduateYear()
	{
		return $this->graduateYear;
	}

	/**
	 * @param mixed $jobAlignment
	 */
	public function setJobAlignment($jobAlignment)
	{
		$this->jobAlignment = $jobAlignment;
	}

	/**
	 * @return mixed
	 */
	public function getJobAlignment()
	{
		return $this->jobAlignment;
	}

	/**
	 * @param mixed $jobType
	 */
	public function setJobType($jobType)
	{
		$this->jobType = $jobType;
	}

	/**
	 * @return mixed
	 */
	public function getJobType()
	{
		return $this->jobType;
	}

	/**
	 * @param mixed $birthdate
	 */
	public function setBirthdate($birthdate)
	{
		$this->birthdate = $birthdate;
	}

	/**
	 * @return mixed
	 */
	public function getBirthdate()
	{
		return $this->birthdate;
	}

	/**
	 * @param mixed $specialization
	 */
	public function setSpecialization($specialization)
	{
		$this->specialization = $specialization;
	}

	/**
	 * @return mixed
	 */
	public function getSpecialization()
	{
		return $this->specialization;
	}
}
