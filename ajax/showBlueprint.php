<?php
/**
 * Show the stats for a blueprint of an item.
 *
 * @package ajax
 * @author  friend8 <map@wafriv.de>
 * @license https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
require_once(__DIR__.'/../lib/config.default.php');
require_once(__DIR__.'/../lib/util/mysql.php');

$blueprint = \Model\Blueprint::loadById($_POST['id']);

echo json_encode($blueprint->getResultingStats());