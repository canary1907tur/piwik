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
}