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
use Doctrine\ORM\EntityRepository;

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
		for ($i = date('Y') + 6; $i > date('Y') - 70; $i--) {
			$years[$i] = $i;
		}

		$builder
			->add('avatar', 'iphp_file', array('label' => 'Аватар', 'required' => false))
			->add('lastName', null, array(
				'label'       => 'Фамилия',
				'constraints' => array(new NotBlank(array(
					'message' => 'Укажите свою фамилию'
				)))
			))
			->add('firstName', null, array(
				'label'       => 'Имя',
				'constraints' => array(new NotBlank(array(
					'message' => 'Укажите свое имя'
				)))
			))
			->add('surName', null, array('label' => 'Отчество', 'required' => false))
			->add('birthdate', 'date', array(
				'label'  => 'Дата рождения',
				'years'  => range(date('Y') - 111, date('Y')),
				'format' => 'dd MMMM yyyy',
			))
			->add('hideBirthdate', null, array('required' => false))
			->add(
				$builder->create('city', 'text', array('label' => 'Город'))->addModelTransformer($cityToStringTransformer)
			)
			->add('phone', null, array('label' => 'Телефон', 'required' => false))
			->add('hidePhone', null, array('required' => false))
			->add('icq', null, array('label' => 'ICQ', 'required' => false))
			->add('hideIcq', null, array('required' => false))
			->add('submit1', 'submit', array('label' => 'Сохранить'))

			###############################################################################################

			->add('university', null, array('label' => 'Выберите учебное заведение из списка', 'required' => false, 'empty_value' => 'выберите'))
			->add('school', null, array('label' => 'Или укажите другое'))
			->add(
				$builder->create('graduateYear', 'choice', array('label' => 'Год окончания учебного заведения', 'choices' => $years, 'empty_value' => 'выберите'))->addModelTransformer($yearToNumberTransformer)
			)
			->add('educationType', 'choice', array('label' => 'Форма обучения', 'required' => false, 'choices' => User::getEducationTypes(), 'empty_value' => 'выберите'))
			->add('primarySpecialty', 'entity', array(
				'label'         => 'Основная специальность',
				'empty_value'   => 'выберите',
				'required'      => true,
				'class'         => 'VidalMainBundle:Specialty',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('s')->orderBy('s.title', 'ASC');
					}
			))
			->add('specialization', null, array('label' => 'Специализация', 'attr' => array('data-help' => 'если есть')))
			->add('academicDegree', 'choice', array('label' => 'Ученая степень', 'choices' => User::getAcademicDegrees(), 'empty_value' => 'выберите'))
			->add('dissertation', null, array('label' => 'Тема диссертации', 'required' => false))
			->add('professionalInterests', null, array('label' => 'Профессиональные интересы', 'required' => false))
			->add('submit2', 'submit', array('label' => 'Сохранить'))

			###############################################################################################

			->add('jobPlace', null, array('label' => 'Место работы', 'required' => false))
			->add('jobSite', null, array('label' => 'Сайт', 'required' => false))
			->add('jobPosition', null, array('label' => 'Должность', 'required' => false))
			->add('jobStage', null, array('label' => 'Стаж работы по специальности', 'required' => false))
			->add('jobAchievements', null, array('label' => 'Достижения', 'required' => false))
			->add('about', null, array('label' => 'О себе', 'required' => false))
			->add('jobPublications', null, array('label' => 'Публикации', 'required' => false))
			->add('submit3', 'submit', array('label' => 'Сохранить'));

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