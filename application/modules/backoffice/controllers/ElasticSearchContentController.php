<?php
/**
 * Rubedo -- ECM solution
 * Copyright (c) 2013, WebTales (http://www.webtales.fr/).
 * All rights reserved.
 * licensing@webtales.fr
 *
 * Open Source License
 * ------------------------------------------------------------------------------------------
 * Rubedo is licensed under the terms of the Open Source GPL 3.0 license. 
 *
 * @category   Rubedo
 * @package    Rubedo
 * @copyright  Copyright (c) 2012-2013 WebTales (http://www.webtales.fr)
 * @license    http://www.gnu.org/licenses/gpl.html Open Source GPL 3.0 license
 */
require_once ('ElasticSearchController.php');

/**
 * Controller providing Elastic Search querying in contents
 *
 *
 *
 * @author dfanchon
 * @category Rubedo
 * @package Rubedo
 *         
 */
class Backoffice_ElasticSearchContentController extends Backoffice_ElasticSearchController
{

    protected $_option = 'content';
}