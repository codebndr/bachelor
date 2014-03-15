<?php

namespace Codebender\SenderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Codebender\UtilitiesBundle\Handler\DefaultHandler;


class DefaultController extends Controller
{
	/**
	 * TFTP Uploading
	 *
	 * @param $utilities_handler
	 * @return JSON encoded response
	 */
	public function tftpAction($utilities_handler = null)
	{
		$response = array("success" => false);
		$data = $this->getRequest()->request->get('data');
		$data = json_decode($data, true);
		if(isset($data["ip"]) && isset($data["bin"]))
		{
			$ip = $data["ip"];
			$bin = $data["bin"];
			if($ip && $bin)
			{
				if($utilities_handler == null)
					$utilities_handler = new DefaultHandler();
				$data = $utilities_handler->get_data($this->container->getParameter('sender'), 'bin', $bin."&ip=".$ip);
				$response = $data;
			}
			else
				$response["output"] = "ip or binary was false and/or zero";
		}
		else
			$response["output"] = "no ip or binary was set";

		return new Response(json_encode($response));
	}
}
