<?php
/**
 * Part of the dsa blacksmith.
 *
 * @package Page
 * @author  friend8 <map@wafriv.de>
 * @license https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
namespace Page;

/**
 * Class for the blueprints page.
 *
 * @package Page
 * @author  friend8 <map@wafriv.de>
 * @license https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
class Blueprints extends \SmartWork\Page
{
	/**
	 * Set the used template.
	 */
	public function __construct()
	{
		parent::__construct('blueprints');
	}

	/**
	 * Add javascripts, handle the removing of blueprints and show the blueprints list.
	 *
	 * @return void
	 */
	public function process()
	{
		$this->getTemplate()->loadJs('addBlueprint');
		$this->getTemplate()->loadJs('jquery.materialSelect');
		$this->getTemplate()->loadJs('jquery.techniqueSelect');
		$this->getTemplate()->loadJs('jquery.blueprint');
		$this->getTemplate()->loadJs('showBlueprint');

		$blueprintListing = \Listing\Blueprints::loadList();
		$itemListing = \Listing\Items::loadList();
		$itemTypeListing = \Listing\ItemTypes::loadList();
		$materialListing = \Listing\Materials::loadList();
		$techniqueListing = \Listing\Techniques::loadList();
		$moneyHelper = new \Helper\Money();

		if ($_GET['remove'])
		{
			$this->removeBlueprint($blueprintListing->getById($_GET['remove']));
		}

		$this->getTemplate()->assign('blueprintListing', $blueprintListing);
		$this->getTemplate()->assign('itemListing', $itemListing);
		$this->getTemplate()->assign('itemTypeListing', $itemTypeListing);
		$this->getTemplate()->assign('materialListing', $materialListing);
		$this->getTemplate()->assign('materialList', json_encode($materialListing->getAsArray()));
		$this->getTemplate()->assign('techniqueListing', $techniqueListing);
		$this->getTemplate()->assign('techniqueList', json_encode($techniqueListing->getAsArray()));
		$this->getTemplate()->assign('currencyList', $moneyHelper->getCurrencyList());
	}

	/**
	 * Remove a blueprint.
	 *
	 * @param \Model\Blueprint $blueprint
	 *
	 * @return void
	 */
	protected function removeBlueprint($blueprint)
	{
		$blueprint->remove();
		redirect('index.php?page=Blueprints');
	}
}