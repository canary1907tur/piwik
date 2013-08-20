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
use Piwik\Visualization\Graph;

/**
 * TODO
 */
class Treemap extends Graph
{
    const ID = 'infoviz-treemap';
    const FOOTER_ICON = 'plugins/TreemapVisualization/images/treemap-icon.png';
    const FOOTER_ICON_TITLE = 'Treemap';

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
        $root = $this->makeNode('treemap-root', $properties['title']);
        $this->addDataTableToNode($root, $dataTable);
        return json_encode($root);
    }

    private function addDataTableToNode(&$node, $dataTable, $tableId = '')
    {
        foreach ($dataTable->getRows() as $rowId => $row) {
            $id = $tableId . '_' . $rowId;
            $columnValue = $row->getColumn($this->metricToGraph) ?: 0;

            $childNode = $this->makeNode($id, $row->getColumn('label'), $data = array('$area' => $columnValue));
            if ($row->getIdSubDataTable() !== null) {
                $this->addSubtableToNode($childNode, $row);
            }

            $node['children'][] = $childNode;
        }
    }

    private function addSubtableToNode(&$childNode, $subTableRow)
    {
        $subTable = $subTableRow->getSubtable();
        $subTable->filter('AddSummaryRow', array(4, Piwik_Translate('General_Others'), $columnToSort = $this->metricToGraph)); //TODO: make constants customizable

        $this->addDataTableToNode($childNode, $subTable, $subTableRow->getIdSubDataTable());
    }

    private function makeNode($id, $title, $data = array())
    {
        return array('id' => $id, 'name' => $title, 'data' => $data, 'children' => array());
    }
}