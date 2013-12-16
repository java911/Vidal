<?php

namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации индекса Эластики
 *
 * @package Vidal\MainBundle\Command
 */
class ElasticIndexCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:elasticindex')
			->setDescription('Creates website index in Elastica');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$elasticaClient = new \Elastica\Client();
		$elasticaIndex  = $elasticaClient->getIndex('website');

		$elasticaIndex->create(
			array(
				'number_of_shards'   => 4,
				'number_of_replicas' => 1,
				'analysis'           => array(
					'analyzer' => array(
						'ru' => array(
							'type'      => 'custom',
							'tokenizer' => 'standard',
							'filter'    => array('lowercase', 'ru_stopwords', 'ru_stemming', 'russian_morphology', 'english_morphology'),
						),
					),
					'filter'   => array(
						'ru_stopwords' => array(
							'type'      => 'stop',
							'stopwords' => 'а,без,более,бы,был,была,были,было,быть,в,вам,вас,весь,во,вот,все,всего,всех,вы,где,да,даже,для,до,его,ее,если,есть,еще,же,за,здесь,и,из,или,им,их,к,как,ко,когда,кто,ли,либо,мне,может,мы,на,надо,наш,не,него,нее,нет,ни,них,но,ну,о,об,однако,он,она,они,оно,от,очень,по,под,при,с,со,так,также,такой,там,те,тем,то,того,тоже,той,только,том,ты,у,уже,хотя,чего,чей,чем,что,чтобы,чье,чья,эта,эти,это,я,a,an,and,are,as,at,be,but,by,for,if,in,into,is,it,no,not,of,on,or,such,that,the,their,then,there,these,they,this,to,was,will,with',
						),
						'ru_stemming'  => array(
							'type'     => 'snowball',
							'language' => 'Russian',
						)
					)
				)
			),
			true
		);

		$output->writeln('+++ vidal:elasticindex created!');
	}
}