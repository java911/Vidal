<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\DrugBundle\Entity\ArticleType;

class ArticleTypesCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:article_types')
			->setDescription('Command to copy');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$titles = array(
			'Описание симптома',
			'Описание заболевания',
			'Профилактика',
			'Лечение',
			'Диагностика',
			'Обзор препаратов',
			'Алгоритмы ведения пациентов',
			'Данные клинических исследований',
			'Особенности применения препарата',
			'Стандарты лечения',
		);

		foreach ($titles as $title) {
			$type = new ArticleType();
			$type->setTitle($title);
			$em->persist($type);
		}

		$em->flush();
	}
}