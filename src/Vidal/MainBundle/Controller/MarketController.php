<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Tests\Extension\Core\DataTransformer\DateTimeToArrayTransformerTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Vidal\MainBundle\Entity\MarketOrder;

use Vidal\MainBundle\Market\Product;
use Vidal\MainBundle\Market\FindDrug;
use Vidal\MainBundle\Market\Basket;
use Vidal\MainBundle\Geo\SxGeo;

class MarketController extends Controller{

    protected $shipping = Array(
        '1' => '99 / 0',
        '2' => '349',
        '3' => '349',
        '4' => '0',
        '5' => '0',
    );

    protected $shippingTitle = array(
        '1' => 'Москва + 10 км МКАД - 99 руб (от 2000 руб - бесплатно)',
        '2' => 'Москва срочная доставка в течение 4х часов - 349 руб',
        '3' => 'Подмосковье - 349 руб',
        '4' => 'Россия (минимальный заказ 2000 руб)',
        '5' => 'Самовывоз',
    );


    protected $shippingEapteka = Array(
        '1' => '100',
        '2' => '150',
        '3' => '0',
        '4' => '300'
    );

    protected $shippingTitleEapteka = array(
        '1' => 'Курьером по Москве в пределах МКАД - 100  р.',
        '2' => 'курьером в отдаленные районы Москвы - 150 р.',
        '3' => 'Самовывоз - 0 р.',
        '4' => 'Срочная доставка в течении 3х часов - 300 р.',
    );


    protected $shippingPiluli = Array(
        '1' => '100',
        '2' => '0',
        '3' => '150',
        '4' => '200',
        '5' => '300',
        '6' => '400',
        '7' => '500',
        '8' => '600',
        '9' => '800',
        '10' => '1000',
    );

    protected $shippingTitlePiluli = array(
        '1' => 'Москва в пределах МКАД, а также районы: Северное и Южное Бутово, Новокосино, Жулебино, Митино - 100 руб.',
        '2' => 'При заказе на сумму от 900 рублей в пределах МКАД, а также районы: Северное и Южное Бутово, Новокосино, Жулебино, Митино - бесплатно',
        '3' => 'Отдаленные районы Москвы Солнцево, Павшинская пойма и др. - 150 руб.',
        '4' => 'Подмосковье до 5 км от МКАД  - 200 руб.',
        '5' => 'Подмосковье 5-10 км от МКАД   - 300 руб.',
        '6' => 'Подмосковье 10-15 км от МКАД - 400 руб.',
        '7' => 'Подмосковье 15-20 км от МКАД  - 500 руб.',
        '8' => 'Подмосковье 20-30 км от МКАД  - 600 руб.',
        '9' => 'Подмосковье 30-40 км от МКАД  - 800 руб.',
        '10' => 'Подмосковье 40-50 км от МКАД  - 1000 руб.',
    );


    /**********************/

    protected $shippingWer = Array(
        '1' => '0',
        '2' => '150',
        '3' => '200',
        '4' => '400',
        '5' => '250',
    );

    protected $shippingTitleWer = array(
        '1' => 'Доставка по Москве при сумме более 3000 руб. - Бесплатно',
        '2' => 'Доставка по Москве в пределах МКАД - 150 руб.',
        '3' => 'Доставка по Москве за пределы МКАДа - 200 руб.',
        '4' => 'Доставка для юридических лиц - 400 руб.',
        '5' => 'Доставка через почту - 250 руб.',
    );





    protected $status = array(
        0 => 'принят',
        1 => 'в обработке',
        2 => 'дозваниваемся',
        3 => 'подтверждён',
        4 => 'доставлен',
        5 => 'отменён',
        6 => 'отправлен',
        7 => 'нет лекарств',
        8 => 'ожидание оплаты',
        9 => 'создаётся оператором',
        10 => 'лекарства заказаны',
        11 => 'недовоз',
        12 => 'возврат от курьеров',
        13 => 'собран',
        14 => 'ожидание товара',
        15 => 'товар проверен',
    );

    /**
     * @Route("/addbsk/{code}/{count}", name="add_to_basket", defaults={"count"="1"}, options={"expose"=true})
     * @Template("VidalMainBundle:Market:list.html.twig")
     */
    public function AddToBasketAction(Request $request, $code, $count = 1){

        $basket = new Basket($request);

        $product = null;
        $product = $basket->getProduct($code);
        if ($product != null ){
            $product->setCount($product->getCount()+$count);
        }else{
            $pr = $this->getDoctrine()->getRepository('VidalMainBundle:MarketDrug')->findOneByCode($code);
            if ($pr != null){
                $product = new Product();
                $product->setCount($count);
                $product->setTitle($pr->getTitle());
                $product->setCode($pr->getCode());
                $product->setGroupApt($pr->getGroupApt());
                $product->setManufacturer($pr->getManufacturer());
                $product->setPrice($pr->getPrice());
            }
        }
        if ($product != null){
            $basket->setProduct($product);
            $basket->save();
        }

        return $this->redirect($this->get('request')->server->get('HTTP_REFERER').'#inbasket');
    }

    /**
     * @Route("/setbsk/{code}/{count}", name="set_to_basket", defaults={"count"="1"}, options={"expose"=true})
     * @Template("VidalMainBundle:Market:list.html.twig")
     */
    public function setToBasketAction(Request $request, $code, $count = 1){
        $basket = new Basket($request);

        $product = null;
        $product = $basket->getProduct($code);
        if ($product != null ){
            $product->setCount($count);
        }else{
            $pr = $this->getDoctrine()->getRepository('VidalMainBundle:MarketDrug')->findOneByCode($code);
            if ($pr != null){
                $product = new Product();
                $product->setCount($count);
                $product->setTitle($pr->getTitle());
                $product->setCode($pr->getCode());
                $product->setGroupApt($pr->getGroupApt());
                $product->setPrice($pr->getPrice());
            }
        }
        if ($product != null){
            $basket->setProduct($product);
            $basket->save();
        }

        return $this->redirect($this->get('request')->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/removebsk/{code}", name="remove_to_basket", options={"expose"=true})
     * @Template("VidalMainBundle:Market:list.html.twig")
     */
    public function removeToBasketAction(Request $request, $code){
        $basket = new Basket($request);
        $product = $basket->get($code);
        $basket->remove($product);
        return $this->redirect($this->get('request')->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/druglist/{drugId}/{isDocs}", name="drug_list", defaults={"isDocs"="false"}, options={"expose"=true})
     * @Template("VidalMainBundle:Market:list.html.twig")
     */
    public function productListAction( $drugId, $isDocs = 'false' ){
        if ( $isDocs == 'false' ){
            $drug = $this->getDoctrine()->getManager('drug')->getRepository('VidalDrugBundle:Product')->findOneBy(array('ProductID' => $drugId));
        }else{
            $drug = $this->getDoctrine()->getManager('drug')->getRepository('VidalDrugBundle:Document')->findOneBy(array('DocumentID' => $drugId));
        }
        if ($drug){
            $RusName = $drug->getRusName();

            $p    = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i');
            $r    = array('', '');

            $title = preg_replace($p, $r, $RusName);
            $first = mb_substr($title,0,2);//первая буква
            $last = mb_substr($title,2);//все кроме первой буквы
            $last = mb_strtolower($last,'UTF-8');
            $title =$first.$last;

            $list = $this->getDoctrine()->getRepository('VidalMainBundle:MarketDrug')->find($title);

            $lists = array();
            foreach ( $list as $drug){
                $lists[$drug->getGroupApt()][] = $drug;
            }

            /**
             * Если не Москва удаляем все позиции WER.ru
             */
            $dir = __DIR__.'/../Geo/SxGeoCity.dat';
            $SxGeo = new SxGeo($dir);

//            $ip = '84.253.73.126';
            $ip = $_SERVER['REMOTE_ADDR'];

            $info = $SxGeo->get($ip);

            if ($info['city']['name_ru']!='Москва'){
                unset($lists['wer']);
            }



        }else{
            $lists = array();
        }

        if ( $list == array() ){
            return new Response('');
        } else{
            return array('lists' => $lists);
        }
    }

    /**
     * @Route("/basketlist", name="basket_list" )
     * @Template("VidalMainBundle:Market:basket_list.html.twig")
     */
    public function basketListAction(Request $request){
        $basket = new Basket($request);
//        $basket->removeAll();
        $products = $basket->getAll();
        $amounts = $basket->getAmounts();
        return array(
            'products' => $products,
            'amounts'  => $amounts,
        );
    }

    /**
     * @Route("/basketorder/{group}", name="basket_order" )
     * @Template("VidalMainBundle:Market:basket_order.html.twig")
     */
    public function basketOrderAction(Request $request, $group){

        $session = $request->getSession();
//        if (!$session->isStarted()){
//            $session->start();
//        }

        if ($group == 'piluli'){
            $array = $this->shippingTitlePiluli;
        }elseif($group == 'eapteka'){
            $array = $this->shippingTitleEapteka;
        }elseif($group == 'wer'){
            $array = $this->shippingTitleWer;
        }else{
            $array = $this->shippingTitle;
        }


        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        # генерация формы
        if ($session->has('userOrder') != null && $session->has('userOrder') != false){
            $order = $session->get('userOrder');
        }else{
            $order   = new MarketOrder();
        }
        $builder = $this->createFormBuilder($order);
        $builder
            ->add('lastName', null, array('label' => 'Фамилия'))
            ->add('firstName', null, array('label' => 'Имя'))
            ->add('surName', null, array('label' => 'Отчество'))
            ->add('email', null, array('label' => 'E-mail'))
            ->add('phone', null, array('label' => 'Телефон'))
            ->add('adress', null, array('label' => 'Адрес'))

            ->add('shipping', 'choice', array('label' => 'Выбор доставки', 'choices' => $array, 'attr' => array( 'class' => 'delivery-select')))
            ->add('comment', null, array('label' => 'Комментарий к доставке'))
            ->add('groupApt', 'hidden')
            ->add('submit', 'submit', array('label' => 'Отправить заказ', 'attr' => array('class' => 'btn-red')));
        $form    = $builder->getForm();
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()){
                $order = $form->getData();
                if ($order->getGroupApt() == 'piluli'){
                    $array = $this->shippingPiluli;
                }else{
                    $array = $this->shippingEapteka;
                }

                $order->setShippingPrice($array[$order->getShipping()]);

                $order->setGroupApt($group);
                $em->persist($order);
                $em->flush($order);
                $em->refresh($order);

                # Заливаем информацию о заказе в сессиюы
                $session->set('userOrder',$order);


                $xml = $this->generateXml($request, $group, $order);
                if ( $xml == false ){
                    return $this->redirect($this->generateUrl('index'));
                }
                $basket = new Basket($request);
                $order->setBody($xml);
                $sum = $basket->getSumma();
                $order->setSum($sum[$group]);
                $succes = false;
                if ($group == 'zdravzona' || $group == 'wer'){
                    $succes = $this->mailSend($group, $order, $basket);
                    $succes = true;
                }else{
                    $succes = $this->emacsSend($group, $order, $basket);
                    $succes = true;
                }
                if ($succes == true ){
                    $order->setEnabled(true);
                    $em->flush($order);

                    $basket->clear($group);
                    if ($group != 'zdravzona' ){
//                        $url = 'http://smacs.ru/feedbacks/'.md5('vidal_ma'.$order->getId().'vidal3L29y4');
                        return $this->render("VidalMainBundle:Market:order_success_2.html.twig",array('group'=>$group,'summPrice'=>$sum[$group]));
                    }else{
                        return $this->render("VidalMainBundle:Market:order_success_2.html.twig",array('group'=>$group,'summPrice'=>$sum[$group]));
                    }
                }else{
                    return array('form' => $form->createView());
                }

            }else{
                return array('form' => $form->createView());
            }
        }else{
            return array('form' => $form->createView());
        }

    }

    /**
     * @Route("/basketcount", name="basket_count" )
     * @Template("VidalMainBundle:Market:basket_count.html.twig")
     */
    public function countProductAction(Request $request){
        $basket = new Basket($request);
        $count = $basket->getCount();

        return array('count' => $count );
    }

    /**
     * @Route("/setbskajax/{code}/{count}", name="set_to_basket_ajax", defaults={"count"="1"}, options={"expose"=true})
     */
    public function setToBasketAjax(Request $request, $code, $count = 1){
        $basket = new Basket($request);
        $summa = 0;
        $product = null;
        $product = $basket->getProduct($code);
        if ($product != null ){
            $product->setCount($count);
        }else{
            $pr = $this->getDoctrine()->getRepository('VidalMainBundle:MarketDrug')->findOneByCode($code);
            if ($pr != null){
                $product = new Product();
                $product->setCount($count);
                $product->setTitle($pr->getTitle());
                $product->setCode($pr->getCode());
                $product->setGroup($pr->getGroup());
                $product->setPrice($pr->getPrice());
            }
        }
        if ($product != null){
            $basket->setProduct($product);
            $basket->save();
            $product = $basket->getProduct($code);

            $summa = $product->getPrice() * $product->getCount();
            $summa =  number_format($summa,2,'.',',');
            $summaAll = $basket->getSumma();
            $s1 = number_format(( isset($summaAll['eapteka']) ? $summaAll['eapteka'] : 0 ),2,'.',',');
            $s2 = number_format(( isset($summaAll['piluli']) ? $summaAll['piluli'] : 0 ),2,'.',',');
            $s3 = number_format(( isset($summaAll['zdravzona']) ? $summaAll['zdravzona'] : 0 ),2,'.',',');
        }
        return new Response($summa.'|'.$s1.'|'.$s2.'|'.$s3);
    }


    public function generateXml($request, $group, $order){

        $basket = new Basket($request);
        $products = $basket->getAll();
        if (!isset($products[$group])){
            return false;
        }
        $products = $products[$group];
        $summa = $basket->getAmounts();
        $summa = $summa[$group];

        if ($group == 'piluli'){
            $array = $this->shippingTitlePiluli;
        }elseif($group == 'eapteka'){
            $array = $this->shippingTitleEapteka;
        }elseif($group == 'wer'){
            $array = $this->shippingTitleWer;
        }else{
            $array = $this->shippingTitle;
        }

        $header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                    <orders>
                        <order>
	                        <order_code>".   "vidal_".$order->getId()."</order_code>
	                        <name>".         $order->getLastName()." ".$order->getFirstName()." ".$order->getSurName()."</name>
                            <phone>".        $order->getPhone()."</phone>
                            <email>".        $order->getEmail()."</email>
                            <address>".      $order->getAdress()."</address>
                            <comment>".      $order->getComment()."</comment>
                            <shipping_id>".  $order->getShipping()."</shipping_id>
                            <shipping_cost>".$array[$order->getShipping()]."</shipping_cost>
                            <payment_id>1</payment_id>
                            <discount>0</discount>
                            <total_cost>".   $summa."</total_cost>
                            <order_time>".   time($order->getCreated())."</order_time>
                            <products>";

        $footer = "         </products>
                        </order>
                    </orders>";

        $xml = '';
        foreach ( $products as $product){
            $xml .= "            <product>
                                    <code>".$product->getCode()."</code>
                                    <name>".$product->getTitle()."</name>
                                    <amount>".$product->getCount()."</amount>
                                    <price>".$product->getPrice()."</price>
                                </product>";
        }

        return $header.$xml.$footer;
    }


    public function mailSend($group, $order,Basket $basket){
        $summa = $basket->getAmounts();
        $summa = $summa[$group];
        $basket = $basket->getAll();
        $basket = $basket[$group];


        if ($group == 'piluli'){
                    $array = $this->shippingTitlePiluli;
        }elseif($group == 'eapteka'){
            $array = $this->shippingTitleEapteka;
        }elseif($group == 'wer'){
            $array = $this->shippingTitleWer;
        }else{
            $array = $this->shippingTitle;
        }
	
	
        if ($group == 'zdravzona'){
            $email = array('zakaz@zdravzona.ru');
        }else{
            $email = array('admin@wer.ru','wer.marketing@ya.ru ');
        }

        # уведомление магазина о покупке
        $this->get('email.service')->send(
//            "tulupov.m@gmail.com",
            $email,
            array('VidalMainBundle:Email:market_notice.html.twig', array('group' => $group, 'order' => $order, 'basket' => $basket, 'summa' => $summa, 'ship' => $array[$order->getShipping()] )),
            'Заказ с сайта Vidal.ru'
        );

        $this->get('email.service')->send(
//            "zakaz@zdravzona.ru",
//            "tulupov.m@gmail.com",
            array($order->getEmail()),
            array('VidalMainBundle:Email:market_notice_user.html.twig', array('group' => $group, 'order' => $order, 'basket' => $basket, 'summa' => $summa, 'ship' => $array[$order->getShipping()] )),
            'Заказ с сайта Vidal.ru'
        );
        return true;
    }

    public function emacsSend($group, $order,Basket $basket){

        if ( $order->getGroupApt() == 'eapteka'){
            $url = 'http://vidal:3L29y4@ea.smacs.ru/exchange';
//            $url = 'vidal.loc/test.php';
        }elseif ( $order->getGroupApt() == 'piluli'){
            $url = 'http://vidal:3L29y4@smacs.ru/exchange';
//                $url = 'vidal.loc/test.php';
        }

        $xml = $order->getBody();

        $xml = simplexml_load_string($xml);
        $xml = $xml->asXML();

        if( $curl = curl_init() ) {

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, 'xml='.$xml);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $data = curl_exec($curl);

            curl_close($curl);

        }
//        $data = $basket->getAmounts();
        $summa = $basket->getSumma();
        $summa = $summa[$group];

        $basket = $basket->getAll();
        $basket = $basket[$group];
        $xml = simplexml_load_string($data);
        if ($xml->error->error_code){
            if ($group == 'piluli'){
                $array = $this->shippingTitlePiluli;
            }elseif($group == 'eapteka'){
                $array = $this->shippingTitleEapteka;
            }elseif($group == 'wer'){
                $array = $this->shippingTitleWer;
            }else{
                $array = $this->shippingTitle;
            }
            $this->get('email.service')->send(
//            "tulupov.m@gmail.com",
                array($order->getEmail()),
                array('VidalMainBundle:Email:market_notice_user.html.twig',
                    array(
                        'group' => $group,
                        'order' => $order,
                        'basket' => $basket,
                        'summa' => $summa,
                        'ship' => $array[$order->getShipping()]
                    )),
                'Заказ с сайта Vidal.ru'
            );
            return true;
        }else{
            return false;
        }
    }


    /**
     * @Route("/drugbutton/{drugId}/{isDocs}", name="drug_button", defaults={"isDocs"="false"}, options={"expose"=true})
     * @Template("VidalMainBundle:Market:button.html.twig")
     */
    public function buttonAction( $drugId, $isDocs = 'false'){
        $count = 0;
        if ( $isDocs == 'false' ){
            $drug = $this->getDoctrine()->getManager('drug')->getRepository('VidalDrugBundle:Product')->findOneBy(array('ProductID' => $drugId));
        }else{
            $drug = $this->getDoctrine()->getManager('drug')->getRepository('VidalDrugBundle:Document')->findOneBy(array('DocumentID' => $drugId));
        }
        if ($drug){
            $RusName = $drug->getRusName();

            $p    = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i');
            $r    = array('', '');

            $title = preg_replace($p, $r, $RusName);
            $first = mb_substr($title,0,2);//первая буква
            $last = mb_substr($title,2);//все кроме первой буквы
            $last = mb_strtolower($last,'UTF-8');
            $title =$first.$last;
            $list = $this->getDoctrine()->getRepository('VidalMainBundle:MarketDrug')->find($title);
            $count = count($list);
        }
        return array('count' => $count);
    }

}
