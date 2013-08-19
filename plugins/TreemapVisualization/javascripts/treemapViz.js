/*!
 * Piwik - Web Analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

(function ($, $jit) {

    var dataTable = window.dataTable,
        dataTablePrototype = dataTable.prototype;

    /**
     * TODO
     */
    window.TreemapDataTable = function () {
        dataTable.call(this);
    }

    $.extend(window.TreemapDataTable.prototype, dataTablePrototype, {

        /**
         * TODO
         */
        init: function (workingDivId, domElem) {
            if (typeof domElem == "undefined") {
                domElem = $('#' + workingDivId);
            }

            dataTablePrototype.init.apply(this, arguments);

            var thisId = domElem.attr('id');
            var treemapContainerId = thisId + '-infoviz-treemap';
            var treemapContainer = $('.infoviz-treemap', domElem).attr('id', treemapContainerId);

            var self = this;
            this.treemap = new $jit.TM.Squarified({
                injectInto: treemapContainerId,
                titleHeight: 15,
                animate: true, // TODO: disable on ipad/w/o native canvas support
                offset: 1,
                Events: {
                    enable: true,
                    onClick: function (node) {
                        if (node) {
                            self.treemap.enter(node);
                        }
                    },
                    onRightClick: function () {
                        self.treemap.out();
                    },
                },
                duration: 1000, // TODO: what is this for?
                Tips: {
                    enable: false, // TODO: enable
                },
                onCreateLabel: function (nodeElement, node) {
                    $(nodeElement).append($('<span></span>').text(node.name).addClass("infoviz-treemap-node-label"));
                },
            });

            var data = JSON.parse(treemapContainer.attr('data-data'));
            this._prependDataTableIdToNodeIds(thisId, data);
            this._setTreemapColors(data);

            this.treemap.loadJSON(data);
            this.treemap.refresh();
        },

        /**
         * TODO
         */
        _prependDataTableIdToNodeIds: function (prefix, data) {
            data.id = prefix + '-' + data.id;

            var children = data.children || [];
            for (var i = 0; i != children.length; ++i) {
                this._prependDataTableIdToNodeIds(prefix, children[i]);
            }
        },

        /**
         * TODO
         * TODO: shouldn't use series colors, color should reperesent the % evolution from past period
         */
        _setTreemapColors: function (root) {
            var seriesColorNames = ['series1', 'series2', 'series3', 'series4', 'series5',
                                    'series6', 'series7', 'series8', 'series9', 'series10'];
            var colors = piwik.ColorManager.getColors('pie-graph-colors', seriesColorNames, true);

            this._setTreemapNodeColors(colors, root, 0);
        },

        /**
         * TODO
         */
         _setTreemapNodeColors: function (colors, node, colorIdx) {
            if (node.children.length) {
                colorIdx = 0;
                for (var i = 0; i != node.children.length; ++i, ++colorIdx) {
                    this._setTreemapNodeColors(colors, node.children[i], colorIdx % colors.length);
                }
            } else {
                node.data.$color = colors[colorIdx];
            }
         },
    });

}(jQuery, window.$jit));