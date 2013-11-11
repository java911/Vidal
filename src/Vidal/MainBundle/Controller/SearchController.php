<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SearchController extends Controller
{
    /**
     * @Route("/poisk_preparatov/{name}~{DocumentID}.htm", name="search_document_id")
     * @Template()
     */
    public function searchDocumentAction($name, $DocumentID = null)
    {
        return array('name' => $name);
    }

	/**
	 * @Route("/poisk_preparatov/{name}__{ProductID}.htm")
	 * @Template()
	 */
	public function searchProductAction($name, $DocumentID = null)
	{
		return array('name' => $name);
	}

	/**
	 * @Route("/poisk_preparatov/{name}.htm")
	 * @Template()
	 */
	public function searchMoleculeAction($name, $DocumentID = null)
	{
		return array('name' => $name);
	}
}
