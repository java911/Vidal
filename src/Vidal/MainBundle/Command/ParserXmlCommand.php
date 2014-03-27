<?php
namespace Vidal\MainBundle\Command;

use Doctrine\Tests\ORM\Functional\NativeQueryTest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\MainBundle\Entity\MarketCache;

/**
 * Команда парсинга XML аптек для кеширования данных
 *
 * @package Vidal\DrugBundle\Command
 */
class ParserXmlCommand extends ContainerAwareCommand
{

    protected $cacheFile_1; # Файл EAPTEKA
    protected $cacheFile_2; # Файл PILULI
    protected $cacheFile_3; # Файл ZDRAVZONA

    protected $url_file_1 = 'http://vidal:3L29y4@ea.smacs.ru/exchange/price';
    protected $url_file_2 = 'http://vidal:3L29y4@smacs.ru/exchange/price';
    protected $url_file_3 = 'http://www.zdravzona.ru/bitrix/catalog_export/yandex_b.php';


    protected function configure()
    {
        $this->setName('vidal:parser:drugs')
            ->setDescription('parser aptek');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('--- vidal:parser started');

        $emDrug = $this->getContainer()->get('doctrine')->getManager('drug');

        $em = $this->getContainer()->get('doctrine')->getManager();


        # очищаем таблицы кеширования
        $em->createQuery('
			DELETE FROM VidalMainBundle:MarketCache mc
		')->execute();

        $em->createQuery('
			DELETE FROM VidalMainBundle:MarketDrug md
		')->execute();

        # Загружаем файлы XML в Кеш
        $this->uploadFiles();

        # Вначале пройдемся по документам ( их тупо меньше )
        $drugs = $emDrug->getRepository('VidalMainBundle:Document')->findAll();

        foreach ( $drugs as $drug){ # Это надолго

            # Добавлем маркет контроллер
            $marketCache = new MarketCache();
            $marketCache->setTarget($drug->getId());
            $marketCache->setDocument(true);

            $em->persist($marketCache);
            $em->flush($marketCache);
            $em->refresh($marketCache);

            # Удаляем из имени всякую хрень
            $RusName = $drug->getRusName;
            $p    = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i');
            $r    = array('', '');
            $name = preg_replace($p, $r, $RusName);


            # Ищем в первом магазине и добавляем оттуда лекартсва
            $array = $this->findShop_1($getRusName);
            foreach($array as $pr){
                $product = new MarketDrug();
                $product->setCode($pr['code']);
                $product->setTitle($pr->['title']);
                $product->setPrice($pr->['price']);
                $product->setManufacturer($pr->['manufacturer']);
                $product->setUrl($pr->['url']);
                $product->setGroup('eapteka');

                $em->persist($product);
                $em->flush($product);
                $em->refresh($product);

                $marketCache->addDrug($product);
                $em->flush();
            }

            # Ищем во втором магазине и добавляем оттуда лекартсва
            $array = $this->findShop_2($getRusName);
            foreach($array as $pr){
                $product = new MarketDrug();
                $product->setCode($pr['code']);
                $product->setTitle($pr->['title']);
                $product->setPrice($pr->['price']);
                $product->setManufacturer($pr->['manufacturer']);
                $product->setUrl($pr->['url']);
                $product->setGroup('piluli');

                $em->persist($product);
                $em->flush($product);
                $em->refresh($product);

                $marketCache->addDrug($product);
                $em->flush();
            }

            # Ищем в третьем магазине и добавляем оттуда лекартсва
            $array = $this->findShop_3($getRusName);
            foreach($array as $pr){
                $product = new MarketDrug();
                $product->setCode($pr['code']);
                $product->setTitle($pr->['title']);
                $product->setPrice($pr->['price']);
                $product->setManufacturer($pr->['manufacturer']);
                $product->setUrl($pr->['url']);
                $product->setGroup('zdravzona');

                $em->persist($product);
                $em->flush($product);
                $em->refresh($product);

                $marketCache->addDrug($product);
                $em->flush();
            }



        }



        $output->writeln('+++ vidal:parser completed!');
    }


    protected function uploadFiles(){
        $this->cacheFile_1 = simplexml_load_file($this->url_file_1);
        $this->cacheFile_3 = simplexml_load_file($this->url_file_2);
        $this->cacheFile_2 = simplexml_load_file($this->url_file_3);

        return true;
    }

    protected function findShop_1($title){
        $elems = $this->cacheFile_1->xpath("product[contains(concat(' ', name, ' '), ' $name ')]");
        $arr = array();
        foreach ($elems as $elem){
            $arr[] = array(
                'code' => $elem->code,
                'manufacturer' => $elem->manufacturer,
                'title' => $elem->name,
                'price' => $elem->price,
                'url' => $this->drugUrl.$elem->code,
            );
        }
        return $arr;
    }

    protected function findShop_2($title){
        $elems = $this->xml->xpath("product[contains(concat(' ', name, ' '), ' $name ')]");
        $arr = array();
        $url = '';
        foreach ($elems as $elem){
            if ( isset($this->arUrl["$elem->code"]) ){
                $url =  $this->arUrl["$elem->code"] ;
                $arr[] = array(
                    'code' => $elem->code,
                    'manufacturer' => $elem->manufacturer,
                    'title' => $elem->name,
                    'price' => $elem->price,
                    'url'   => $url,
                );
            }
        }
        return $arr;
    }

    protected function findShop_3($title){

    }
}