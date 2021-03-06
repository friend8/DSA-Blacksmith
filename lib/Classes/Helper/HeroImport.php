<?php
/**
 * Part of the dsa blacksmith.
 *
 * @package Helper
 * @author  friend8 <map@wafriv.de>
 * @license https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
namespace Helper;

/**
 * Import a hero created with the program Heldensoftware
 *
 * @package Helper
 * @author  friend8 <map@wafriv.de>
 * @license https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
class HeroImport
{
    /**
     * @var string
     */
    protected $xml;

    /**
     * Mapping for the talents of the Helden Software xml export.
     *
     * @var array
     */
    protected $talentMapping = array(
        'Bogenbau' => 'bowMaking',
        'Feinmechanik' => 'precisionMechanics',
        'Grobschmied' => 'blacksmith',
        'Holzbearbeitung' => 'woodworking',
        'Lederarbeiten' => 'leatherworking',
        'Schneidern' => 'tailoring',
    );

    /**
     * @param string $xml
     */
    function __construct($xml)
    {
        $this->xml = $xml;
    }

    /**
     * Import a character from the Helden Software xml export.
     *
     * @return \Model\Character
     */
    public function import()
    {
        $simplexml = simplexml_load_string($this->xml);
        /* @var $characterXml \SimpleXMLElement */
        $characterXml = $simplexml->held;
        $characterAttributes = $characterXml->attributes();
        $character = array(
            'user' => \SmartWork\User::getUserById($_SESSION['userId']),
            'key' => $characterAttributes['key'],
            'lastUpdate' => \DateTime::createFromFormat('U', intval(floatval($characterAttributes['stand']) / 1000)),
            'name' => $characterAttributes['name'],
            'bowMaking' => 0,
            'precisionMechanics' => 0,
            'blacksmith' => 0,
            'woodworking' => 0,
            'leatherworking' => 0,
            'tailoring' => 0,
        );
        /* @var $talents \SimpleXMLElement */
        $talents = $characterXml->talentliste;

        /* @var $talent \SimpleXMLElement */
        foreach ($talents->talent as $talent)
        {
            $talentAttributes = $talent->attributes();

            if (array_key_exists(strval($talentAttributes['name']), $this->talentMapping))
            {
                $character[$this->talentMapping[strval($talentAttributes['name'])]] = intval($talentAttributes['value']);
            }
        }

        return \Model\Character::create($character);
    }
}
