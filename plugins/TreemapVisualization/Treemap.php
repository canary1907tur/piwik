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

use Piwik\View;
use Piwik\DataTable;
use Piwik\Visualization\Graph;

/**
 * TODO
 */
class Treemap extends Graph
{
    const ID = 'infoviz-treemap';
    const FOOTER_ICON = 'plugins/TreemapVisualization/images/treemap-icon.png';
    const FOOTER_ICON_TITLE = 'Treemap';

    public static $clientSideProperties = array('filter_offset');

    /**
     * TODO
     */
    private $metricToGraph;

    /**
     * TODO
     */
    public function __construct($view)
    {
        parent::__construct($view);

        $view->datatable_js_type = 'TreemapDataTable';
        $view->request_parameters_to_modify['expanded'] = 1;
        $view->request_parameters_to_modify['depth'] = 1;
    }

    /**
     * TODO
     */
    public function render($dataTable, $properties)
    {
        $firstColumn = reset($properties['columns_to_display']);
        if ($firstColumn == 'label') {
            $firstColumn = next($properties['columns_to_display']);
        }
        $this->metricToGraph = $firstColumn;

        $view = new View('@TreemapVisualization/_dataTableViz_treemap.twig');
        $view->graphData = $this->getGraphData($dataTable, $properties);
        $view->properties = $properties;
        return $view->render();
    }

    /**
     * TODO
     */
    public static function getDefaultPropertyValues()
    {
        $result = parent::getDefaultPropertyValues();
        $result['visualization_properties']['graph']['max_graph_elements'] = 6;
        return $result;
    }

    private function getGraphData($dataTable, $properties)
    {
        $generator = new TreemapDataGenerator();
        $generator->setRootNodeName($properties['title']);
        $generator->setInitialRowOffset($properties['filter_offset'] ?: 0);
        return json_encode($generator->generate($dataTable));
    }
}