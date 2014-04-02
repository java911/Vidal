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
class ParserXmlCommand extends ContainerAwareCommand
{

    protected $cacheFile_1; # Файл EAPTEKA
    protected $cacheFile_2; # Файл PILULI
    protected $cacheFile_3; # Файл ZDRAVZONA

    protected $url_file_1 = 'http://vidal:3L29y4@ea.smacs.ru/exchange/price';
    protected $url_file_2 = 'http://vidal:3L29y4@smacs.ru/exchange/price';
    protected $url_file_3 = 'http://www.zdravzona.ru/bitrix/catalog_export/yandex_b.php';

    protected $arUrl; # Для пилюль список URL

    protected function configure()
    {
        $this->setName('vidal:parser:drugs')
            ->setDescription('parser aptek');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('--- vidal:parser started');
        # Подключаем пилюли URL
        include 'piluliCodeUrl.php';
        $this->arUrl = $mass;

        $emDrug = $this->getContainer()->get('doctrine')->getManager('drug');
        $em = $this->getContainer()->get('doctrine')->getManager();

        $em->createQuery('
			DELETE FROM VidalMainBundle:MarketDrug md
		')->execute();

        # Загружаем файлы XML в Кеш
        $this->uploadFiles();



//        # Ищем в первом магазине и добавляем оттуда лекарства
        $array = $this->findShop_1('');
        $c1 = count($array);
        $output->writeln('<error> Count => '.$c1.'</error>');
        $i = 0;
        foreach($array as $pr){
            $i ++ ;
            $product = new MarketDrug();
            $product->setCode($pr['code']);
            $product->setTitle($pr['title']);
            $product->setPrice($pr['price']);
            $product->setManufacturer($pr['manufacturer']);
            $product->setUrl($pr['url']);
            $product->setGroupApt('eapteka');
            $em->persist($product);
            $em->flush($product);
            $output->writeln('<comment>'.$i.' : '.$product->getTitle().'</comment>');
        }

        # Ищем во втором магазине и добавляем оттуда лекартсва
        $array = $this->findShop_2('');
        $c2 = count($array);
        $output->writeln('<error> Count => '.$c2.'</error>');
        $i = 0;
        foreach($array as $pr){
            $i ++ ;
            $product = new MarketDrug();
            $product->setCode($pr['code']);
            $product->setTitle($pr['title']);
            $product->setPrice($pr['price']);
            $product->setManufacturer($pr['manufacturer']);
            $product->setUrl($pr['url']);
            $product->setGroupApt('piluli');
            $em->persist($product);
            $em->flush($product);
            $output->writeln('<comment>'.$i.' : '.$product->getTitle().'</comment>');
        }

        # Ищем в третьем магазине и добавляем оттуда лекартсва
        $array = $this->findShop_3('');
        $c3 = count($array);
        $output->writeln('<error> Count => '.$c3.'</error>');
        $i = 0;
        foreach($array as $pr){
            $i ++ ;
            $product = new MarketDrug();
            $product->setCode($pr['code']);
            $product->setTitle($pr['title']);
            $product->setPrice($pr['price']);
            $product->setManufacturer($pr['manufacturer']);
            $product->setUrl($pr['url']);
            $product->setGroupApt('zdravzona');
            $em->persist($product);
            $em->flush($product);
            $output->writeln('<comment>'.$i.' : '.$product->getTitle().'</comment>');
        }
        $output->writeln('<error>'.$c1.' - '.$c2.' - '.$c3.'</error>');

        $output->writeln('+++ vidal:parser completed!');
    }


    protected function uploadFiles(){
        $this->cacheFile_1 = simplexml_load_file($this->url_file_1);
        $this->cacheFile_2 = simplexml_load_file($this->url_file_2);
        $this->cacheFile_3 = simplexml_load_file($this->url_file_3);

        return true;
    }

    protected function findShop_1($title){
        #$elems = $this->cacheFile_1->xpath("product[contains(concat(' ', name, ' '), ' $title ')]");
        $elems =  $this->cacheFile_1;
        $arr = array();
        $drugUrl = 'http://www.eapteka.ru/goods/drugs/otolaryngology/rhinitis/?id=';
        foreach ($elems as $elem){
            $arr[] = array(
                'code' => $elem->code,
                'manufacturer' => $elem->manufacturer,
                'title' => $elem->name,
                'price' => $elem->price,
                'url' => $drugUrl.$elem->code,
            );
        }
        return $arr;
    }

    protected function findShop_2($title){
        #$elems = $this->cacheFile_2->xpath("product[contains(concat(' ', name, ' '), ' $title ')]");
        $elems =  $this->cacheFile_2;
        $arr = array();
        $drugUrl = 'http://www.piluli.ru/product';
        foreach ($elems as $elem){
            if (isset($this->arUrl["$elem->code"])){
                $url =  $this->arUrl["$elem->code"] ;
            }else{
                $url = '';
            }
            $arr[] = array(
                'code' => $elem->code,
                'manufacturer' => ( isset($elem->manufacturer) ? $elem->manufacturer : '' ),
                'title' => $elem->name,
                'price' => $elem->price,
                'url'   => $url,
            );
        }
        return $arr;
    }

    protected function findShop_3($title){
        $elems = $this->cacheFile_3->xpath("shop/offers/offer[contains(concat(' ',model, ' '), ' $title ')]");
        $arr = array();
        foreach ($elems as $elem){
            $arr[] = array(
                'code'      => $elem['id'],
                'manufacturer' => $elem->vendor,
                'title' => $elem->model,
                'price' => $elem->price,
                'url' => $elem->url,
            );
        }
        return $arr;
    }
}