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
 * @see plugins/TreemapVisualization/Treemap.php
 */
require_once PIWIK_INCLUDE_PATH . '/plugins/TreemapVisualization/Treemap.php';

/**
 * Plugin that contains the Treemap DataTable visualization.
 */
class TreemapVisualization extends \Piwik\Plugin
{
    /**
     * @see Piwik_Plugin::getListHooksRegistered
     */
    public function getListHooksRegistered()
    {
        return array(
            'AssetManager.getCssFiles'            => 'getCssFiles',
            'AssetManager.getJsFiles'             => 'getJsFiles',
            'DataTableVisualization.getAvailable' => 'getAvailableDataTableVisualizations',
            'ViewDataTable.configureReportView'   => 'enableTreemapForActions'
        );
    }

    public function getAvailableDataTableVisualizations(&$visualizations)
    {
        $visualizations[] = 'Piwik\\Plugins\\TreemapVisualization\\Treemap';
    }

    public function getCssFiles(&$cssFiles)
    {
        $cssFiles[] = 'plugins/TreemapVisualization/stylesheets/treemap.less';
    }

    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = 'libs/Jit/jit-2.0.1-yc.js';
        $jsFiles[] = 'plugins/TreemapVisualization/javascripts/treemapViz.js';
    }

    public function enableTreemapForActions($view)
    {
        // make sure treemap is shown on actions reports
        if (strpos($view->getReportApiMethod(), 'Actions.') === 0) {
            $view->show_all_views_icons = true;
            $view->show_bar_chart = false;
            $view->show_pie_chart = false;
            $view->show_tag_cloud = false;
        }
    }
}