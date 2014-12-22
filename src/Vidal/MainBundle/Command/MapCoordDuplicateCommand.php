<?php
namespace Vidal\MainBundle\Command;

use Doctrine\Tests\ORM\Functional\NativeQueryTest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\MainBundle\Entity\MarketCache;
use Vidal\MainBundle\Entity\MarketDrug;

/**
 * Команда парсинга XML аптек для кеширования данных
 *
 * @package Vidal\DrugBundle\Command
 */
class MapCoordDuplicateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('vidal:mapcoord:duplicate')
            ->setDescription('Removed dumplicates by mapcoord.offerId');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('--- vidal:mapcoord:duplicate started');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $pdo = $em->getConnection();

        $stmt = $pdo->prepare('DELETE m1 FROM mapcoords m1, mapcoords m2 WHERE m1.id > m2.id AND m1.offerId = m2.offerId');
        $stmt->execute();

        $output->writeln('+++ vidal:mapcoord:duplicate completed!');
    }
}