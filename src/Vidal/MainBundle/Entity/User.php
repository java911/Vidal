<?php
namespace Vidal\MainBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(name="user")
 * @FileStore\Uploadable
 */
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
	 * @ORM\Column(type="array", nullable=true)
	 * @FileStore\UploadableField(mapping="avatar")
	 * @Assert\Image(
	 *        maxSize="2M",
	 *        maxSizeMessage="Принимаются фотографии размером до 2 Мб"
	 * )
	 */
	protected $avatar;

	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(message="Укажите свое имя")
	 */
	protected $firstName;

	/**
	 * @ORM\Column(type="string")
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

	/** @ORM\ManyToOne(targetEntity="University", inversedBy="doctors") */
	protected $university;

	/**
	 * @ORM\Column(type="date", nullable=true)
	 * @Assert\NotBlank(message = "Укажите год окончания ВУЗа")
	 */
	protected $graduateYear;

	/** @ORM\Column(type="date", nullable=true) */
	protected $birthdate;

	/** @ORM\Column(type="boolean") */
	protected $hideBirthdate;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @Assert\NotBlank(message="Укажите ученую степень")
	 * @Assert\Choice(callback="getAcademicDegrees", message="Некорректная ученая степень. Пожалуйста, выберите из списка.")
	 */
	protected $academicDegree;

	/** @ORM\Column(length=255, nullable=true) */
	protected $phone;

	/** @ORM\Column(type="boolean") */
	protected $hidePhone;

	/** @ORM\Column(length=255, nullable=true) */
	protected $icq;

	/** @ORM\Column(type="boolean") */
	protected $hideIcq;

	/** @ORM\Column(length=30, nullable=true) */
	protected $educationType;

	/** @ORM\Column(length=500, nullable=true) */
	protected $dissertation;

	/** @ORM\Column(type="text", nullable=true) */
	protected $professionalInterests;

	/** @ORM\Column(length=255, nullable=true) */
	protected $jobPlace;

	/**
	 * @ORM\Column(length=255, nullable=true)
	 * @Assert\Url(message="Сайт указан некорректно")
	 */
	protected $jobSite;

	/** @ORM\Column(length=255, nullable=true) */
	protected $jobPosition;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $jobStage;

	/** @ORM\Column(type="text", nullable=true) */
	protected $jobAchievements;

	/** @ORM\Column(type="text", nullable=true) */
	protected $about;

	/** @ORM\Column(type="text", nullable=true) */
	protected $jobPublications;

	/** @ORM\Column(length=255, nullable=true) */
	protected $oldCompany;

	/** @ORM\Column(length=255, nullable=true) */
	protected $oldLogin;

	/** @ORM\Column(type="boolean") */
	protected $oldUser;

	/**
	 * @ORM\OneToMany(targetEntity="QuestionAnswer", mappedBy="answerUser")
	 */
	protected $answers;

	/** @ORM\Column(length=255, nullable=true) */
	protected $school;

	/** @ORM\ManyToOne(targetEntity="Region", inversedBy="doctors") */
	protected $region;

	/** @ORM\ManyToOne(targetEntity="Country", inversedBy="doctors") */
	protected $country;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $confirmation = 0;

	/**
	 * @ORM\Column(type="array", nullable=true)
	 * @FileStore\UploadableField(mapping="docs")
	 */
	protected $confirmationScan;

	/** @ORM\Column(type="boolean") */
	protected $unsibscribed = false;

	public function __construct()
	{
		$this->confirmationScan = array();
		$this->answers          = new ArrayCollection();
		$this->emailConfirmed   = false;
		$this->hideBirthdate    = false;
		$this->hidePhone        = false;
		$this->hideIcq          = false;
		$this->oldUser          = false;
		$this->confirmation     = false;
		$this->unsubscribed     = false;
		$this->roles            = 'ROLE_UNCONFIRMED';
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

	public static function getEducationTypes()
	{
		return array('Очная' => 'Очная', 'Заочная' => 'Заочная');
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

	public function refreshPassword()
	{
		$password = substr(chr(rand(103, 122)) . chr(rand(103, 122)) . chr(rand(103, 122)) . md5(time() + rand(100, 999) . chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122))), 0, 8);
		$this->setPassword($password);

		return $password;
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

	public function refreshHash()
	{
		$this->hash = md5(time() . $this->getUsername() . $this->getPassword());
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

	/**
	 * @param mixed $avatar
	 */
	public function setAvatar($avatar)
	{
		$this->avatar = $avatar;
	}

	/**
	 * @return mixed
	 */
	public function getAvatar()
	{
		return $this->avatar;
	}

	public function resetAvatar()
	{
		$this->avatar = array();

		return $this;
	}

	/**
	 * @param mixed $hideBirthdate
	 */
	public function setHideBirthdate($hideBirthdate)
	{
		$this->hideBirthdate = $hideBirthdate;
	}

	/**
	 * @return mixed
	 */
	public function getHideBirthdate()
	{
		return $this->hideBirthdate;
	}

	/**
	 * @param mixed $icq
	 */
	public function setIcq($icq)
	{
		$this->icq = $icq;
	}

	/**
	 * @return mixed
	 */
	public function getIcq()
	{
		return $this->icq;
	}

	/**
	 * @param mixed $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;
	}

	/**
	 * @return mixed
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * @param mixed $hideIcq
	 */
	public function setHideIcq($hideIcq)
	{
		$this->hideIcq = $hideIcq;
	}

	/**
	 * @return mixed
	 */
	public function getHideIcq()
	{
		return $this->hideIcq;
	}

	/**
	 * @param mixed $hidePhone
	 */
	public function setHidePhone($hidePhone)
	{
		$this->hidePhone = $hidePhone;
	}

	/**
	 * @return mixed
	 */
	public function getHidePhone()
	{
		return $this->hidePhone;
	}

	/**
	 * @param mixed $dissertation
	 */
	public function setDissertation($dissertation)
	{
		$this->dissertation = $dissertation;
	}

	/**
	 * @return mixed
	 */
	public function getDissertation()
	{
		return $this->dissertation;
	}

	/**
	 * @param mixed $educationType
	 */
	public function setEducationType($educationType)
	{
		$this->educationType = $educationType;
	}

	/**
	 * @return mixed
	 */
	public function getEducationType()
	{
		return $this->educationType;
	}

	/**
	 * @param mixed $professionalInterests
	 */
	public function setProfessionalInterests($professionalInterests)
	{
		$this->professionalInterests = $professionalInterests;
	}

	/**
	 * @return mixed
	 */
	public function getProfessionalInterests()
	{
		return $this->professionalInterests;
	}

	/**
	 * @param mixed $jobPlace
	 */
	public function setJobPlace($jobPlace)
	{
		$this->jobPlace = $jobPlace;
	}

	/**
	 * @return mixed
	 */
	public function getJobPlace()
	{
		return $this->jobPlace;
	}

	/**
	 * @param mixed $jobPosition
	 */
	public function setJobPosition($jobPosition)
	{
		$this->jobPosition = $jobPosition;
	}

	/**
	 * @return mixed
	 */
	public function getJobPosition()
	{
		return $this->jobPosition;
	}

	/**
	 * @param mixed $jobSite
	 */
	public function setJobSite($jobSite)
	{
		$this->jobSite = $jobSite;
	}

	/**
	 * @return mixed
	 */
	public function getJobSite()
	{
		return $this->jobSite;
	}

	/**
	 * @param mixed $about
	 */
	public function setAbout($about)
	{
		$this->about = $about;
	}

	/**
	 * @return mixed
	 */
	public function getAbout()
	{
		return $this->about;
	}

	/**
	 * @param mixed $jobAchievements
	 */
	public function setJobAchievements($jobAchievements)
	{
		$this->jobAchievements = $jobAchievements;
	}

	/**
	 * @return mixed
	 */
	public function getJobAchievements()
	{
		return $this->jobAchievements;
	}

	/**
	 * @param mixed $jobPublications
	 */
	public function setJobPublications($jobPublications)
	{
		$this->jobPublications = $jobPublications;
	}

	/**
	 * @return mixed
	 */
	public function getJobPublications()
	{
		return $this->jobPublications;
	}

	/**
	 * @param mixed $jobStage
	 */
	public function setJobStage($jobStage)
	{
		$this->jobStage = $jobStage;
	}

	/**
	 * @return mixed
	 */
	public function getJobStage()
	{
		return $this->jobStage;
	}

	/**
	 * @param mixed $oldCompany
	 */
	public function setOldCompany($oldCompany)
	{
		$this->oldCompany = $oldCompany;
	}

	/**
	 * @return mixed
	 */
	public function getOldCompany()
	{
		return $this->oldCompany;
	}

	/**
	 * @param mixed $oldLogin
	 */
	public function setOldLogin($oldLogin)
	{
		$this->oldLogin = $oldLogin;
	}

	/**
	 * @return mixed
	 */
	public function getOldLogin()
	{
		return $this->oldLogin;
	}

	/**
	 * @param mixed $oldUser
	 */
	public function setOldUser($oldUser)
	{
		$this->oldUser = $oldUser;
	}

	/**
	 * @return mixed
	 */
	public function getOldUser()
	{
		return $this->oldUser;
	}

	/**
	 * @param mixed $answers
	 */
	public function setAnswers($answers)
	{
		$this->answers = $answers;
	}

	/**
	 * @return mixed
	 */
	public function getAnswers()
	{
		return $this->answers;
	}

	/**
	 * @param mixed $school
	 */
	public function setSchool($school)
	{
		$this->school = $school;
	}

	/**
	 * @return mixed
	 */
	public function getSchool()
	{
		return $this->school;
	}

	/**
	 * @param mixed $region
	 */
	public function setRegion($region)
	{
		$this->region = $region;
	}

	/**
	 * @return mixed
	 */
	public function getRegion()
	{
		return $this->region;
	}

	/**
	 * @param mixed $country
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}

	/**
	 * @return mixed
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * @param mixed $confirmation
	 */
	public function setConfirmation($confirmation = 0)
	{
		$this->confirmation = $confirmation;
	}

	/**
	 * @return mixed
	 */
	public function getConfirmation()
	{
		return $this->confirmation;
	}

	/**
	 * @param mixed $confirmationScan
	 */
	public function setConfirmationScan($confirmationScan)
	{
		$this->confirmationScan = $confirmationScan;
	}

	/**
	 * @return mixed
	 */
	public function getConfirmationScan()
	{
		return $this->confirmationScan;
	}

	public function resetConfirmationScan()
	{
		$this->confirmationScan = array();

		return $this;
	}

    /**
     * @param boolean $unsubscribed
     */
    public function setUnsubscribed($unsubscribed = false)
    {
        $this->unsubscribed = $unsubscribed;
    }

    /**
     * @return boolean
     */
    public function getUnsubscribed()
    {
        return $this->unsubscribed;
    }



}
