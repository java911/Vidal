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

class ProfileType extends AbstractType
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
			->add('lastName', null, array('label' => 'Фамилия'))
			->add('firstName', null, array('label' => 'Имя'))
			->add('surName', null, array('label' => 'Отчество', 'required' => false))
			->add('birthdate', 'date', array(
				'label'  => 'Дата рождения',
				'years'  => range(date('Y') - 111, date('Y')),
				'format' => 'dd MMMM yyyy',
			))
			->add(
				$builder->create('city', 'text', array('label' => 'Город'))->addModelTransformer($cityToStringTransformer)
			)
			->add('university', null, array('label' => 'ВУЗ', 'required' => true, 'empty_value' => 'выберите'))
			->add('student', null, array('label' => 'Являюсь студентом', 'required' => false))
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
			->add('specialization', null, array('label' => 'Специализация', 'attr' => array('data-help' => 'если есть')))
			->add('academicDegree', 'choice', array('label' => 'Ученая степень', 'choices' => User::getAcademicDegrees(), 'empty_value' => 'выберите'))
			->add('jobType', 'choice', array('label' => 'Место работы', 'choices' => User::getJobTypes(), 'empty_value' => 'выберите'))
			->add('jobAlignment', 'choice', array('label' => 'Вид организации', 'choices' => User::getJobAlignments(), 'empty_value' => 'выберите'))
			->add('submit', 'submit', array('label' => 'Сохранить'));

	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array('data_class' => 'Vidal\MainBundle\Entity\User'));
	}

	public function getName()
	{
		return 'profile';
	}
}