<?php
namespace Vidal\MainBundle\Command;

use Doctrine\Tests\ORM\Functional\NativeQueryTest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\MainBundle\Entity\MapRegion;
use Vidal\MainBundle\Entity\MapCoord;

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

        $this->dir = '/var/www/upload_vidal/map/map/';

        $em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln('--- vidal:parser started');
        # Подключаем пилюли URL

        if (is_dir($this->dir)) {
            if ($dh = opendir($this->dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (filetype($this->dir . $file) == 'file'){
                        $str = "Файл: ( ".$this->dir .$file .": тип: " . filetype($this->dir . $file);
                        $output->writeln($str);
                        $body = file_get_contents($this->dir . $file );
                        $json = json_decode($body);
//                        var_dump($json);
//                        exit;
                        $region = new MapRegion();
                        $region->setTitle($file);

                        $em->persist($region);
                        $em->flush($region);
                        $em->refresh($region);

                        foreach( $json as $key => $val){

                            $coord = $em->getRepository('VidalMainBundle:MapCoord')->findOneById($val->id);
                            if ($coord == null){
                                $coord = new MapCoord();
                                $coord->setOfferId($val->id);
                                $coord->setLatitude($val->Latitude);
                                $coord->setLongitude($val->Longitude);
                                $coord->setRegion($region);
                                $em->persist($coord);
                                $em->flush($coord);
                                $output->writeln("<comment>\t".$val->id."</comment>");
                            }
                        }

                    }
                }
                closedir($dh);
            }
        }
    }

}