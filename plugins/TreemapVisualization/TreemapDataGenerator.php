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

use Piwik\DataTable;

/**
 * TODO
 */
class TreemapDataGenerator
{
    /**
     * TODO
     */
    private $rootName = '';

    /**
     * TODO
     */
    private $firstRowOffset = 0;

    /**
     * TODO
     */
    private $metricToGraph;

    /**
     * TODO
     */
    public function __construct($metricToGraph)
    {
        $this->metricToGraph = $metricToGraph;
    }

    /**
     * TODO
     */
    public function setRootNodeName($name)
    {
        $this->rootName = $name;
    }

    /**
     * TODO
     */
    public function setInitialRowOffset($offset)
    {
        $this->firstRowOffset = (int)$offset;
    }

    /**
     * TODO
     */
    public function generate($dataTable)
    {
        $root = $this->makeNode('treemap-root', $this->rootName);
        $this->addDataTableToNode($root, $dataTable, $tableId = '', $this->firstRowOffset);
        return $root;
    }

    private function addDataTableToNode(&$node, $dataTable, $tableId = '', $offset = 0)
    {
        foreach ($dataTable->getRows() as $rowId => $row) {
            $id = $this->getNodeId($tableId, $rowId);
            $columnValue = $row->getColumn($this->metricToGraph) ?: 0;

            $childNode = $this->makeNode($id, $row->getColumn('label'), $data = array('$area' => $columnValue));

            if ($rowId == DataTable::ID_SUMMARY_ROW) {
                $childNode['data']['aggregate_offset'] = $offset + $dataTable->getRowsCount() - 1;
            } else if ($row->getIdSubDataTable() !== null) {
                $childNode['data']['idSubtable'] = $row->getIdSubDataTable();
                
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

    private function getNodeId($tableId, $rowId)
    {
        if ($rowId == DataTable::ID_SUMMARY_ROW) {
            $rowId = $this->firstRowOffset . '_' . $rowId;
        } else {
            $rowId = $this->firstRowOffset += $rowId;
        }

        return $tableId . '_' . $rowId;
    }

    private function makeNode($id, $title, $data = array())
    {
        return array('id' => $id, 'name' => $title, 'data' => $data, 'children' => array());
    }
}