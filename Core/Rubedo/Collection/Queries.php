<?php
/**
 * Rubedo -- ECM solution
 * Copyright (c) 2012, WebTales (http://www.webtales.fr/).
 * All rights reserved.
 * licensing@webtales.fr
 *
 * Open Source License
 * ------------------------------------------------------------------------------------------
 * Rubedo is licensed under the terms of the Open Source GPL 3.0 license. 
 *
 * @category   Rubedo
 * @package    Rubedo
 * @copyright  Copyright (c) 2012-2012 WebTales (http://www.webtales.fr)
 * @license    http://www.gnu.org/licenses/gpl.html Open Source GPL 3.0 license
 */
namespace Rubedo\Collection;

use Rubedo\Interfaces\Collection\IQuerys,Rubedo\Services\Manager;

/**
 * Service to handle Querys
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 */
class Querys extends AbstractCollection implements IQuerys
{
	

	public function __construct(){
		$this->_collectionName = 'Querys';
		parent::__construct();
	}
	
}