<?php

namespace Helbrary\NetteTesterExtension;

use Nette\Application\Responses\RedirectResponse;
use Nette\Application\Responses\TextResponse;
use Nette\Security\IAuthenticator;
use Nette\Utils\Strings;


abstract class BasePresenterTester extends PresenterTester
{

	/** @var string */
	protected $presenterName;


	/**
	 * BasePresenterTester constructor.
	 * @param string $presenterName - etc. 'Front:GoodsChange:Goods'
	 * @param string $bootstrapPath
	 */
	public function __construct($presenterName, $bootstrapPath = __DIR__ . '/../../../../app/bootstrap.php')
	{
	    parent::__construct($bootstrapPath);
	    $this->setPresenterName($presenterName);
	}


}
