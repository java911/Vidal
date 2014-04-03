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
class ParserMapCommand extends ContainerAwareCommand
{

    protected $dir;

    protected function configure()
    {
        $this->setName('vidal:parser:map')
            ->setDescription('parser aptek');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->dir = '/var/www/upload_vidal/map/';

        $output->writeln('--- vidal:parser started');
        # Подключаем пилюли URL

        if (is_dir($this->dir)) {
            if ($dh = opendir($this->dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (filetype($this->dir . $file) == 'file'){
                        $str = "Файл: $file : тип: " . filetype($this->dir . $file);
                        $output->writeln($str);



                    }
                }
                closedir($dh);
            }
        }



    }

}