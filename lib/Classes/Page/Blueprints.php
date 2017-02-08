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
        $this->getTemplate()->loadJs('jquery.ajax');
        $this->getTemplate()->loadJs('removeRow');
        $this->getTemplate()->loadJs('jquery.addTalentPoints');
        $this->getTemplate()->loadJs('jquery.materialSelect');
        $this->getTemplate()->loadJs('jquery.techniqueSelect');
        $this->getTemplate()->loadJs('jquery.popupBlueprint');
        $this->getTemplate()->loadJs('jquery.popupEdit');
        $this->getTemplate()->loadJs('blueprint');
        $this->getTemplate()->loadJsReadyScript('
            $(document).tooltip({
                content: function () {
                    $(this).addClass("tooltip");
                    return $(this).attr("title").replace(/(?:\r\n|\r|\n)/g, "<br />");
                }
            });
            $(".addTalentPoints").addTalentPoints();
        ');

        $blueprintListing = \Listing\Blueprints::loadList();
        $itemListing = \Listing\Items::loadList();
        $itemTypeListing = \Listing\ItemTypes::loadList();
        $materialListing = \Listing\Materials::loadList();
        $techniqueListing = \Listing\Techniques::loadList();
        $moneyHelper = new \Helper\Money();

        switch ($_GET['action']) {
            case 'remove':
                $this->removeBlueprint($blueprintListing->getById($_GET['id']));
                break;
            case 'edit':
                $this->editBlueprint($_GET['id'], $_POST['data']);
                break;
            case 'get':
                $this->getBlueprint($blueprintListing->getById($_GET['id']));
                break;
            case 'stats':
                $this->getStats($blueprintListing->getById($_GET['id']));
                break;
        }

        $translator = \SmartWork\Translator::getInstance();
        $this->getTemplate()->assign('blueprintListing', $blueprintListing);
        $this->getTemplate()->assign('itemListing', $itemListing);
        $this->getTemplate()->assign('itemTypeListing', $itemTypeListing);
        $this->getTemplate()->assign('materialListing', $materialListing);
        $this->getTemplate()->assign('materialList', json_encode($materialListing->getAsArray()));
        $this->getTemplate()->assign('techniqueListing', $techniqueListing);
        $this->getTemplate()->assign('techniqueList', json_encode($techniqueListing->getAsArray()));
        $this->getTemplate()->assign('currencyList', $moneyHelper->getCurrencyList());
        $talentList = array(
            'bowMaking' => $translator->gt('bowMaking'),
            'precisionMechanics' => $translator->gt('precisionMechanics'),
            'blacksmith' => $translator->gt('blacksmith'),
            'woodworking' => $translator->gt('woodworking'),
            'leatherworking' => $translator->gt('leatherworking'),
            'tailoring' => $translator->gt('tailoring'),
        );
        asort($talentList, SORT_NATURAL);
        $this->getTemplate()->assign('talentList', json_encode($talentList));
        $this->assign('columsPerItemType', array(
            'meleeWeapon' => array(
                'blueprint',
                'item',
                'itemType',
                'damageType',
                'materials',
                'techniques',
                'upgradeHitPoints',
                'upgradeBreakFactor',
                'upgradeInitiative',
                'upgradeWeaponModificator',
            ),
            'rangedWeapon' => array(
                'blueprint',
                'item',
                'itemType',
                'damageType',
                'materials',
                'bonusRangedFightValue',
                'reducePhysicalStrengthRequirement',
            ),
            'projectile' => array(
                'blueprint',
                'item',
                'itemType',
                'projectileForItem',
                'materials',
            ),
        ));
    }

    /**
     * Remove a blueprint.
     *
     * @param \Model\Blueprint $blueprint
     *
     * @return void
     */
    protected function removeBlueprint(\Model\Blueprint $blueprint)
    {
        $blueprint->remove();
        $this->doRender = false;

        $this->echoAjaxResponse(array('ok' => true));
    }

    /**
     * Get a single blueprints stats.
     * Used for ajax requests.
     *
     * @param \Model\Blueprint $blueprint
     *
     * @return void
     */
    protected function getStats(\Model\Blueprint $blueprint)
    {
        $this->doRender = false;

        if (empty($blueprint))
        {
            $this->echoAjaxResponse(
                array(
                    'ok' => false,
                    'error' => 'noBlueprintFound',
                )
            );
        }
        else
        {
            $this->echoAjaxResponse(
                array(
                    'ok' => true,
                    'data' => $blueprint->getResultingStats()
                )
            );
        }
    }

    /**
     * Get a single blueprints stats.
     * Used for ajax requests.
     *
     * @param \Model\Blueprint $blueprint
     *
     * @return void
     */
    protected function getBlueprint(\Model\Blueprint $blueprint)
    {
        $this->doRender = false;

        if (empty($blueprint))
        {
            $this->echoAjaxResponse(
                array(
                    'ok' => false,
                    'error' => 'noBlueprintFound',
                )
            );
        }
        else
        {
            $this->echoAjaxResponse(
                array(
                    'ok' => true,
                    'data' => $this->getBlueprintData($blueprint),
                )
            );
        }
    }

    /**
     * Add or edit a blueprint.
     * Used for ajax requests.
     *
     * @param integer/string $id
     * @param array          $data
     *
     * @return void
     */
    protected function editBlueprint($id, $data)
    {
        $this->doRender = false;

        if ($id == 'new')
        {
            $response = array(
                'ok' => \Model\Blueprint::create(
                    array_merge(array('userId' => $_SESSION['userId']), $data)
                ),
            );
        }
        else
        {
            $blueprint = \Model\Blueprint::loadById($id);
            $response = array(
                'ok' => $blueprint->update($data),
                'data' => $this->getBlueprintData($blueprint),
            );
        }

        $this->echoAjaxResponse($response);
    }

    /**
     * Prepare the blueprint data for the ajax request so it can be shown in the
     * edit popup.
     *
     * @param \Model\Blueprint $blueprint
     *
     * @return array
     */
    protected function getBlueprintData(\Model\Blueprint $blueprint)
    {
        $blueprintArray = $blueprint->getAsArray();
        $blueprintArray['itemId'] = $blueprint->getItem()->getItemId();
        $blueprintArray['itemTypeId'] = $blueprint->getItemType()->getItemTypeId();
        $blueprintArray['projectileForItemId'] = $blueprint->getProjectileForItem() ? $blueprint->getProjectileForItem()->getItemId() : null;
        $blueprintArray['materials'] = array();
        $blueprintArray['techniques'] = array();

        foreach ($blueprint->getMaterialList() as $entry)
        {
            $blueprintArray['materials'][] = array(
                'material' => $entry['material']->getMaterialId(),
                'percentage' => $entry['percentage'],
                'talent' => $entry['talent'],
            );
        }

        /* @var $technique \Model\Technique */
        foreach ($blueprint->getTechniqueList() as $technique)
        {
            $blueprintArray['techniques'][] = $technique->getTechniqueId();
        }

        $blueprintArray['upgradeWeaponModificator'] = $blueprint->getUpgradeWeaponModificator()[0];

        return $blueprintArray;
    }
}
