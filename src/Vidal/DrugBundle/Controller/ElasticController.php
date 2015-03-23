<?php

namespace Vidal\DrugBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ElasticController extends Controller
{
	/** @Route("/elastic/autocomplete/{type}/{term}", name="elastic_autocomplete", options={"expose":true}) */
	public function autocompleteAction($type, $term)
	{
		$words  = explode(' ', $term);
		$query  = implode('* ', $words) . '*';
		$client = new \Elasticsearch\Client();

		$s['index'] = 'website';
		$s['type']  = 'autocomplete';

		$s['body']['size']                                                 = 15;
		$s['body']['query']['filtered']['query']['query_string']['query']  = $query;
		$s['body']['query']['filtered']['query']['query_string']['fields'] = array('name', 'type');
		$s['body']['highlight']['fields']['name']                          = array("fragment_size" => 100);

		$s['body']['sort']['type']['order'] = 'desc';
		$s['body']['sort']['name']['order'] = 'asc';

		if ($type != 'all') {
			$s['body']['query']['filtered']['filter']['term']['type'] = $type;
		}

		$results = $client->search($s);

		return new JsonResponse($results);
	}

	/** @Route("/elastic/autocomplete_ext/{type}/{term}", name="elastic_autocomplete_ext", options={"expose":true}) */
	public function autocompleteExtAction($type, $term)
	{
		$words  = explode(' ', $term);
		$query  = implode('* ', $words) . '*';
		$client = new \Elasticsearch\Client();

		$s['index'] = 'website';
		$s['type']  = 'autocomplete_ext';

		$s['body']['size']                                                 = 15;
		$s['body']['query']['filtered']['query']['query_string']['query']  = $query;
		$s['body']['query']['filtered']['query']['query_string']['fields'] = array('name', 'type');
		$s['body']['highlight']['fields']['name']                          = array("fragment_size" => 100);

		$s['body']['sort']['type']['order'] = 'desc';
		$s['body']['sort']['name']['order'] = 'asc';

		if ($type != 'all') {
			$s['body']['query']['filtered']['filter']['term']['type'] = $type;
		}

		$results = $client->search($s);

		return new JsonResponse($results);
	}

	/** @Route("/elastic/autocomplete_nozology/{term}", name="elastic_autocomplete_nozology", options={"expose":true}) */
	public function autocompleteNozologyAction($term)
	{
		$words  = explode(' ', $term);
		$query  = implode('* ', $words) . '*';
		$client = new \Elasticsearch\Client();

		$s['index'] = 'website';
		$s['type']  = 'autocomplete_nozology';

		$s['body']['size']                                                 = 15;
		$s['body']['query']['filtered']['query']['query_string']['query']  = $query;
		$s['body']['query']['filtered']['query']['query_string']['fields'] = array('code', 'name');
		$s['body']['highlight']['fields']['name']                          = array("fragment_size" => 100);
		$s['body']['sort']['name']['order']                                = 'asc';

		$results = $client->search($s);

		return new JsonResponse($results);
	}

	/** @Route("/elastic/autocomplete_article/{term}", name="elastic_autocomplete_article", options={"expose":true}) */
	public function autocompleteArticleAction($term)
	{
		$words  = explode(' ', $term);
		$query  = implode('* ', $words) . '*';
		$client = new \Elasticsearch\Client();

		$s['index'] = 'website';
		$s['type']  = 'autocomplete_article';

		$s['body']['size']                                                 = 15;
		$s['body']['query']['filtered']['query']['query_string']['query']  = $query;
		$s['body']['query']['filtered']['query']['query_string']['fields'] = array('title');
		$s['body']['highlight']['fields']['title']                          = array("fragment_size" => 100);
		$s['body']['sort']['title']['order']                               = 'asc';

		$results = $client->search($s);

		return new JsonResponse($results);
	}

	/** @Route("/elastic/autocomplete_pharm/{term}", name="elastic_autocomplete_pharm", options={"expose":true}) */
	public function autocompletePharmAction($term)
	{
		$words  = explode(' ', $term);
		$query  = implode('* ', $words) . '*';
		$client = new \Elasticsearch\Client();

		$s['index'] = 'website';
		$s['type']  = 'autocomplete_pharm';

		$s['body']['size']                                                 = 15;
		$s['body']['query']['filtered']['query']['query_string']['query']  = $query;
		$s['body']['query']['filtered']['query']['query_string']['fields'] = array('name');
		$s['body']['highlight']['fields']['name']                          = array("fragment_size" => 100);
		$s['body']['sort']['name']['order']                                = 'asc';

		$results = $client->search($s);

		return new JsonResponse($results);
	}

	/** @Route("/elastic/autocomplete_product/{term}", name="elastic_autocomplete_product", options={"expose":true}) */
	public function autocompleteProductAction($term)
	{
		$words  = explode(' ', $term);
		$query  = implode('* ', $words) . '*';
		$client = new \Elasticsearch\Client();

		$s['index'] = 'website';
		$s['type']  = 'autocomplete_product';

		$s['body']['size']                                                 = 15;
		$s['body']['query']['filtered']['query']['query_string']['query']  = $query;
		$s['body']['query']['filtered']['query']['query_string']['fields'] = array('name');
		$s['body']['highlight']['fields']['name']                          = array("fragment_size" => 100);
		$s['body']['sort']['name']['order']                                = 'asc';

		$results = $client->search($s);

		return new JsonResponse($results);
	}

	/** @Route("/elastic/autocomplete_interaction/{term}", name="elastic_autocomplete_interaction", options={"expose":true}) */
	public function autocompleteInteractionAction($term)
	{
		$words  = explode(' ', $term);
		$query  = implode('* ', $words) . '*';
		$client = new \Elasticsearch\Client();

		$s['index'] = 'website';
		$s['type']  = 'autocomplete_interaction';

		$s['body']['size']                                                 = 15;
		$s['body']['query']['filtered']['query']['query_string']['query']  = $query;
		$s['body']['query']['filtered']['query']['query_string']['fields'] = array('name');
		$s['body']['highlight']['fields']['name']                          = array("fragment_size" => 100);
		$s['body']['sort']['name']['order']                                = 'asc';

		$results = $client->search($s);

		return new JsonResponse($results);
	}

	/** @Route("/elastic/autocomplete_city/{term}", name="elastic_autocomplete_city", options={"expose":true}) */
	public function autocompleteCityAction($term)
	{
		$words  = explode(' ', $term);
		$query  = implode('* ', $words) . '*';
		$client = new \Elasticsearch\Client();

		$s['index'] = 'website';
		$s['type']  = 'autocomplete_city';

		$s['body']['size']                                                 = 15;
		$s['body']['query']['filtered']['query']['query_string']['query']  = $query;
		$s['body']['query']['filtered']['query']['query_string']['fields'] = array('name');
		$s['body']['highlight']['fields']['name']                          = array("fragment_size" => 100);
		$s['body']['sort']['name']['order']                                = 'asc';

		$results = $client->search($s);

		return new JsonResponse($results);
	}
}
