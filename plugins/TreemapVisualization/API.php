<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik_Plugins
 * @package TreemapVisualization
 */
namespace Piwik\Plugins\TreemapVisualization;

/**
 * TODO
 */
class API
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * TODO
     */
    public function getTreemapData($apiModule, $apiMethod, $idSite, $date, $period, $segment = false)
    {
        // TODO
    }
}