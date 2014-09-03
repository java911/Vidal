<?php
namespace Vidal\MainBundle\Command;
ini_set('memory_limit', -1);
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
    protected $cacheFile_4; # Файл НОВАЯ

    protected $url_file_1 = 'http://vidal:3L29y4@ea.smacs.ru/exchange/price';
    protected $url_file_2 = 'http://vidal:3L29y4@smacs.ru/exchange/price';
    protected $url_file_3 = 'http://www.zdravzona.ru/bitrix/catalog_export/yandex_b.php';
    protected $url_file_4 = 'http://www.wer.ru/catalog_export/vidal.xml';
//    protected $url_file_3 = 'http://www.zdravzona.ru/bitrix/catalog_export/yandex_b.php';

    protected $arUrl; # Для пилюль список URL

    protected function configure()
    {
        $this->setName('vidal:parser:drugs')
            ->setDescription('parser aptek')
            ->addOption('val', null, InputOption::VALUE_REQUIRED, '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $val = $input->getOption('val');
        $output->writeln('--- vidal:parser started');
        # Подключаем пилюли URL
        include 'piluliCodeUrl.php';
        $this->arUrl = $mass;

        $emDrug = $this->getContainer()->get('doctrine')->getManager('drug');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em2 = $this->getContainer()->get('doctrine')->getManager('drug');
//
//        $em->createQuery('
//			DELETE FROM VidalMainBundle:MarketDrug md
//		')->execute();

        # Загружаем файлы XML в Кеш
        $this->uploadFiles();


//        # Ищем в первом магазине и добавляем оттуда лекарства
        if ($val == 1){

            $em->createQuery('
                DELETE FROM VidalMainBundle:MarketDrug md
            ')->execute();

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
        }

        # Ищем во втором магазине и добавляем оттуда лекартсва
        if ($val == 2){
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
        }

        if ($val == 3){
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
        }

        if ($val == 4){
            # Ищем в третьем магазине и добавляем оттуда лекартсва
            $array = $this->findShop_4('');
            $c3 = count($array);
            $output->writeln('<error> Count => '.$c3.'</error>');
            $i = 0;
            foreach($array as $pr){
//                print_r($pr);
                $i ++ ;
//                if ($i > 10) exit;
                $product = new MarketDrug();
                $product->setCode($pr['code']);
                $product->setTitle($pr['title']);
                $product->setPrice($pr['price']);
                $product->setManufacturer($pr['manufacturer']);
                $product->setUrl($pr['url']);
                $product->setGroupApt('wer');
                $em->persist($product);
                $em->flush($product);
                $output->writeln('<comment>'.$i.' : '.$product->getTitle().'</comment>');
            }
        }

        if ($val == 5){
            # Ищем в третьем магазине и добавляем оттуда лекартсва
            $i = 0;
            $docs = $em2->getRepository('VidalDrugBundle:Document')->findAll();
            foreach($docs as $val){
                $title = str_replace("'",'',$val->getRusName());
                $title = str_replace("<SUP>",'',$title);
                $title = str_replace("</SUP>",'',$title);
                $title = str_replace("&reg;",'',$title);
                $t = $em->getRepository('VidalMainBundle:MarketDrug')->find($title);
                $d = count($t);
                $i += ($d >1 ? 1 : 0);
                $output->writeln('<comment>'.$title.' : '.$d.'</comment>');
            }
            $output->writeln('<comment> : '.$i.'</comment>');
        }



        $output->writeln('+++ vidal:parser completed!');
    }


    protected function uploadFiles(){
        $this->cacheFile_1 = simplexml_load_file($this->url_file_1);
        $this->cacheFile_2 = simplexml_load_file($this->url_file_2);
        $this->cacheFile_3 = simplexml_load_file($this->url_file_3);
        $data = file_get_contents($this->url_file_4);
        $data = str_replace('&','%25',$data);
        $this->cacheFile_4 = simplexml_load_string($data);

        return true;
    }

    protected function findShop_1($title){
//        $elems = $this->cacheFile_1->xpath("product[contains(concat(' ', name, ' '), ' $title ')]");
        $elems =  $this->cacheFile_1;
        $arr = array();
        $drugUrl = 'http://www.eapteka.ru/goods/drugs/otolaryngology/rhinitis/?id=';
//        print_r($elems);
//        exit;
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

    protected function findShop_4(){
        $elems =  $this->cacheFile_4;
        $arr = array();
        $i = 0;
        foreach ($elems->Worksheet->Table->Row as $elem){
            $i++;
            if ($i != 1){
                $arr[] = array(
                    'code' => (string) $elem->Cell[0]->Data[0],
                    'manufacturer' => (string) $elem->Cell[2]->Data[0],
                    'title' => (string) $elem->Cell[1]->Data[0],
                    'price' => (string) $elem->Cell[3]->Data[0],
                    'url'   => (string) $elem->Cell[6]->Data[0],
                );
            }
        }
//        print_r($arr);
//        exit;
        return $arr;
    }

    protected function findShop_3($title){
        $elems = $this->cacheFile_3->shop->offers->offer;
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