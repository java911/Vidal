<?php

namespace Vidal\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\True;
use Doctrine\ORM\EntityManager;
use Vidal\MainBundle\Form\DataTransformer\CityToStringTransformer;
use Vidal\MainBundle\Form\DataTransformer\YearToNumberTransformer;

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
			->add('surName', null, array('label' => 'Отчество'))
			->add('birthdate', 'date', array(
				'label'           => 'Дата рождения',
				'widget'          => 'single_text',
				'format'          => 'dd.MM.yyyy',
				'invalid_message' => 'Дата указана неверно. Формат: 31.01.1970',
				'attr'            => array('class' => 'input-calendar', 'title' => '31.01.1970 как пример'),
			))
			->add($builder->create('city', 'text', array(
					'label' => 'Город',
				))->addModelTransformer($cityToStringTransformer)
			)
			->add('specialties', null, array(
				'label'       => 'Специальности',
				'empty_value' => 'выберите',
				'required'    => true,
				'attr'        => array('data-placeholder' => 'выберите одну или несколько')
			))
			->add('university', null, array(
				'label'    => 'ВУЗ',
				'required' => true,
				'attr'     => array('data-placeholder' => 'выберите')
			))
			->add($builder->create('graduateYear', 'choice', array(
					'label'       => 'Год окончания',
					'choices'     => $years,
					'empty_value' => 'выберите',
					'attr'        => array('data-placeholder' => 'выберите')
				))->addModelTransformer($yearToNumberTransformer)
			)
			->add('job', null, array(
				'label'    => 'Место работы',
				'required' => true,
				'attr'     => array('class' => 'input-document')
			))
			->add('phone', null, array(
				'label'    => 'Контактный телефон',
				'required' => true,
			))
			->add('scanDiplom', 'iphp_file', array(
				'label'    => 'Диплом ВУЗа',
				'required' => false,
			))
			->add('scanSpecialist', 'iphp_file', array(
				'label'    => 'Сертификат специалиста',
				'required' => false,
			))
			->add('scanJob', 'iphp_file', array(
				'label'    => 'Справка с работы',
				'required' => false,
			))
			->add('avatar', 'iphp_file', array(
				'label'    => 'Фотография',
				'required' => false,
			))
			->add('submit', 'submit', array('label' => 'СОХРАНИТЬ', 'attr' => array('class' => 'btn-red')));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array('data_class' => 'Learning\MainBundle\Entity\User'));
	}

	public function getName()
	{
		return 'profile';
	}
}