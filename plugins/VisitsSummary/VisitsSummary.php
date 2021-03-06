<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik_Plugins
 * @package VisitsSummary
 */
namespace Piwik\Plugins\VisitsSummary;

use Piwik\WidgetsList;

/**
 * Note: This plugin does not hook on Daily and Period Archiving like other Plugins because it reports the
 * very core metrics (visits, actions, visit duration, etc.) which are processed in the Core
 * Day class directly.
 * These metrics can be used by other Plugins so they need to be processed up front.
 *
 * @package VisitsSummary
 */
class VisitsSummary extends \Piwik\Plugin
{
    /**
     * @see Piwik_Plugin::getListHooksRegistered
     */
    public function getListHooksRegistered()
    {
        return array(
            'API.getReportMetadata' => 'getReportMetadata',
            'WidgetsList.add'       => 'addWidgets',
            'Menu.add'              => 'addMenu',
        );
    }

    public function getReportMetadata(&$reports)
    {
        $reports[] = array(
            'category'         => Piwik_Translate('VisitsSummary_VisitsSummary'),
            'name'             => Piwik_Translate('VisitsSummary_VisitsSummary'),
            'module'           => 'VisitsSummary',
            'action'           => 'get',
            'metrics'          => array(
                'nb_uniq_visitors',
                'nb_visits',
                'nb_actions',
                'nb_actions_per_visit',
                'bounce_rate',
                'avg_time_on_site' => Piwik_Translate('General_VisitDuration'),
                'max_actions'      => Piwik_Translate('General_ColumnMaxActions'),
// Used to process metrics, not displayed/used directly
//								'sum_visit_length',
//								'nb_visits_converted',
            ),
            'processedMetrics' => false,
            'order'            => 1
        );
    }

    function addWidgets()
    {
        WidgetsList::add('VisitsSummary_VisitsSummary', 'VisitsSummary_WidgetLastVisits', 'VisitsSummary', 'getEvolutionGraph', array('columns' => array('nb_visits')));
        WidgetsList::add('VisitsSummary_VisitsSummary', 'VisitsSummary_WidgetVisits', 'VisitsSummary', 'getSparklines');
        WidgetsList::add('VisitsSummary_VisitsSummary', 'VisitsSummary_WidgetOverviewGraph', 'VisitsSummary', 'index');
    }

    function addMenu()
    {
        Piwik_AddMenu('General_Visitors', '', array('module' => 'VisitsSummary', 'action' => 'index'), true, 10);
        Piwik_AddMenu('General_Visitors', 'VisitsSummary_SubmenuOverview', array('module' => 'VisitsSummary', 'action' => 'index'), true, 1);
    }
}


