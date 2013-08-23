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
use Piwik\View;
use Piwik\Visualization\Graph;

/**
 * TODO
 */
class Treemap extends Graph
{
    const ID = 'infoviz-treemap';
    const FOOTER_ICON = 'plugins/TreemapVisualization/images/treemap-icon.png';
    const FOOTER_ICON_TITLE = 'Treemap';

    public static $clientSideProperties = array('filter_offset', 'max_graph_elements');

    /**
     * TODO
     */
    public function __construct($view)
    {
        parent::__construct($view);

        $view->datatable_js_type = 'TreemapDataTable';
        $view->request_parameters_to_modify['expanded'] = 1;
        $view->request_parameters_to_modify['depth'] = 1;
        $view->show_pagination_control = false;
        $view->show_offset_information = false;

        $self = $this;
        $view->filters[] = function ($dataTable, $view) use ($self) {
            $view->custom_parameters['columns'] = $self->getMetricToGraph($view->columns_to_display);
        };
    }

    /**
     * TODO
     */
    public function render($dataTable, $properties)
    {
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
        $generator = new TreemapDataGenerator($this->getMetricToGraph($properties['columns_to_display'])); // TODO: doesn't need to be a private property
        $generator->setRootNodeName($properties['title']);
        $generator->setInitialRowOffset($properties['filter_offset'] ?: 0);
        return Common::json_encode($generator->generate($dataTable));
    }

    public function getMetricToGraph($columnsToDisplay)
    {
        $firstColumn = reset($columnsToDisplay);
        if ($firstColumn == 'label') {
            $firstColumn = next($columnsToDisplay);
        }
        return $firstColumn;
    }
}