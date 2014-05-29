<?php
namespace Vidal\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Vidal\MainBundle\Form\DataTransformer\YearToNumberTransformer;
use Vidal\MainBundle\Form\DataTransformer\CityToStringTransformer;
use Doctrine\ORM\EntityRepository;

class UserAdmin extends Admin
{
	protected $datagridValues;

	public function __construct($code, $class, $baseControllerName)
	{
		parent::__construct($code, $class, $baseControllerName);

		if (!$this->hasRequest()) {
			$this->datagridValues = array(
				'_page'       => 1,
				'_per_page'   => 25,
				'_sort_order' => 'ASC',
				'_sort_by'    => 'title'
			);
		}
	}

	protected function configureFormFields(FormMapper $formMapper)
	{
		$em                      = $this->modelManager->getEntityManager('Vidal\MainBundle\Entity\User');
		$yearToNumberTransformer = new YearToNumberTransformer($em);
		$cityToStringTransformer = new CityToStringTransformer($em);

		$formMapper
			->add('username', null, array('label' => 'E-mail', 'required' => true))
			->add('oldLogin', null, array('label' => 'Логин', 'required' => false))
			->add('avatar', 'iphp_file', array('label' => 'Аватарка', 'required' => false))
			->add('firstName', null, array('label' => 'Имя', 'required' => true))
			->add('lastName', null, array('label' => 'Фамилия', 'required' => true))
			->add('surName', null, array('label' => 'Отчество', 'required' => false))
			->add($formMapper->create('city', 'text', array(
				'label' => 'Город',
			))->addModelTransformer($cityToStringTransformer))
			->add('emailConfirmed', null, array('label' => 'e-mail подтвержден', 'required' => false))
			->add('specialization', null, array('label' => 'Специализация', 'required' => false))
			->add('primarySpecialty', null, array('label' => 'Основная специальность', 'required' => false))
			->add('university', null, array('label' => 'ВУЗ', 'required' => false))
			->add('school', null, array('label' => 'Учебное заведение'))
			->add($formMapper->create('graduateYear', 'text', array(
				'label'    => 'Год выпуска',
				'required' => false,
			))->addModelTransformer($yearToNumberTransformer))
			->add('educationType', null, array('label' => 'Форма обучения', 'required' => false))
			->add('academicDegree', null, array('label' => 'Ученая степень', 'required' => false))
			->add('birthdate', null, array('label' => 'Дата рождения', 'required' => false))
			->add('icq', null, array('label' => 'ICQ', 'required' => false))
			->add('dissertation', null, array('label' => 'Тема диссертации', 'required' => false))
			->add('professionalInterests', null, array('label' => 'Профессиональные интересы', 'required' => false))
			->add('jobPlace', null, array('label' => 'Место работы', 'required' => false))
			->add('jobSite', null, array('label' => 'Сайт', 'required' => false))
			->add('jobPosition', null, array('label' => 'Должность', 'required' => false))
			->add('jobStage', null, array('label' => 'Стаж работы по специальности', 'required' => false))
			->add('about', null, array('label' => 'О себе', 'required' => false))
			->add('jobPublications', null, array('label' => 'Публикации', 'required' => false))
			->add('oldUser', null, array('label' => 'Со старого сайта', 'required' => false));
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$em = $this->modelManager->getEntityManager('Vidal\MainBundle\Entity\User');

		$datagridMapper
			->add('id')
			->add('username', null, array('label' => 'E-mail'))
			->add('lastName', null, array('label' => 'Фамилия'))
			->add('primarySpecialty', null, array('label' => 'Основная специальность'))
			->add('city', null, array(
				'label'         => 'Город',
				'class'         => 'VidalMainBundle:City',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('c')
							->where('c.country = 1')
							->andWhere('SIZE(c.doctors) > 0')
							->orderBy('s.title', 'ASC');
					}
			))
			->add('region', null, array(
				'label'         => 'Область',
				'class'         => 'VidalMainBundle:Region',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('r')
							->where('r.country = 1')
							->andWhere('SIZE(r.doctors) > 0')
							->orderBy('r.title', 'ASC');
					}
			))
			->add('country', null, array(
				'label'         => 'Страна',
				'class'         => 'VidalMainBundle:Country',
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('c')
							->where('c.country = 1')
							->andWhere('SIZE(c.doctors) > 0')
							->orderBy('s.title', 'ASC');
					}
			))
			->add('emailConfirmed', null, array('label' => 'e-mail подтвержден'))
			->add('oldUser', null, array('label' => 'Со старого сайта'));
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->add('id')
			->add('username', null, array('label' => 'E-mail'))
			->add('emailConfirmed', null, array('label' => 'Подтвердил', 'template' => 'VidalDrugBundle:Sonata:swap_emailConfirmed.html.twig'))
			->add('lastName', null, array('label' => 'Фамилия И.О.', 'template' => 'VidalDrugBundle:Sonata:user_fio.html.twig'))
			->add('login', null, array('label' => 'Прежний логин'))
			->add('primarySpecialty', null, array('label' => 'Основная специальность'))
			->add('birthdate', null, array('label' => 'Дата рождения'))
			->add('city', null, array('label' => 'Город'))
			->add('region', null, array('label' => 'Область'))
			->add('oldUser', null, array('label' => 'Со старого сайта'))
			->add('_action', 'actions', array(
				'label'   => 'Действия',
				'actions' => array(
					'edit' => array(),
				)
			));
	}
}