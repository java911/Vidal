<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SearchController extends Controller
{
	/**
	 * @Route("/poisk_preparatov/{EngName}__{ProductID}.htm", name="search_product")
	 * @Template()
	 */
	public function searchProductAction($EngName, $DocumentID = null)
	{
		return array();
	}

    /**
     * @Route("/poisk_preparatov/{EngName}~{DocumentID}.htm", name="search_document_id")
	 * @Route("/poisk_preparatov/{EngName}.htm", name="search_document")
     * @Template()
     */
    public function searchDocumentAction($EngName, $DocumentID = null)
    {
		$em = $this->getDoctrine()->getManager();

		//$document =

        return array();
    }
}
