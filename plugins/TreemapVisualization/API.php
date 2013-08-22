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

use Piwik\Common;
use Piwik\API\Request;

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
    public function getTreemapData($apiModule, $apiMethod, $columns)
    {
        $dataTable = Request::processRequest("$apiModule.$apiMethod");

        $columns = explode(',', $columns);
        $column = reset($columns);

        $generator = new TreemapDataGenerator($column);
        $generator->setInitialRowOffset(Common::getRequestVar('filter_offset', 0, 'int'));
        return $generator->generate($dataTable);
    }
}