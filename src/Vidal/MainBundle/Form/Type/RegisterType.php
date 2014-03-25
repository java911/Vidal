<?php

namespace Vidal\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\True;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Doctrine\ORM\EntityManager;
use Vidal\MainBundle\Form\DataTransformer\CityToStringTransformer;
use Vidal\MainBundle\Form\DataTransformer\YearToNumberTransformer;
use Vidal\MainBundle\Entity\User;

class RegisterType extends AbstractType
{
	protected $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$cityToStringTransformer = new CityToStringTransformer($this->em);
		$yearToNumberTransformer = new YearToNumberTransformer($this->em);

		$years = array();
		for ($i = date('Y'); $i > date('Y') - 70; $i--) {
			$years[$i] = $i;
		}

		$builder
			->add('username', null, array('label' => 'E-mail'))
			->add('password', 'password', array(
				'label'       => 'Придумайте пароль',
				'constraints' => array(new Regex(array(
					'pattern' => '/[а-яА-Я]/',
					'match'   => false,
					'message' => 'Русские буквы в пароле недопустимы'
				)))
			))
			->add('lastName', null, array('label' => 'Фамилия'))
			->add('firstName', null, array('label' => 'Имя'))
			->add('surName', null, array('label' => 'Отчество', 'required' => false))
			->add('birthdate', 'date', array(
				'label'  => 'Дата рождения',
				'years'  => range(date('Y') - 111, date('Y')),
				'data'   => new \DateTime('1970-01-01'),
				'format' => 'dd MMMM yyyy',
			))
			->add(
				$builder->create('city', 'text', array('label' => 'Город'))->addModelTransformer($cityToStringTransformer)
			)
			->add('university', null, array('label' => 'ВУЗ', 'required' => true, 'empty_value' => 'выберите'))
			->add($builder->create('graduateYear', 'choice', array(
					'label'       => 'Год окончания',
					'required'    => true,
					'choices'     => $years,
					'empty_value' => 'выберите',
					'attr'        => array('data-placeholder' => 'выберите')
				))->addModelTransformer($yearToNumberTransformer)
			)
			->add(
				$builder->create('graduateYear', 'choice', array('label' => 'Год окончания ВУЗа', 'choices' => $years, 'empty_value' => 'выберите'))->addModelTransformer($yearToNumberTransformer)
			)
			->add('primarySpecialty', null, array(
				'label'       => 'Основная специальность',
				'empty_value' => 'выберите',
				'required'    => true,
			))
			->add('academicDegree', 'choice', array('label' => 'Ученая степень', 'choices' => User::getAcademicDegrees(), 'empty_value' => 'выберите'))
			->add('eula', 'checkbox', array(
				'label'       => 'Пользовательское соглашение',
				'mapped'      => false,
				'required'    => false,
				'constraints' => new True(array(
						'message' => 'Пожалуйста, подтвердите что вы согласны с пользовательским соглашением'
					))
			))
			->add('submit', 'submit', array('label' => 'Зарегистрироваться'));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array('data_class' => 'Vidal\MainBundle\Entity\User'));
	}

	public function getName()
	{
		return 'register';
	}
}