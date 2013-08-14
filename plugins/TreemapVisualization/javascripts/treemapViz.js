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
                    $(nodeElement).text(node.name);
                },
            });

            var data = JSON.parse(treemapContainer.attr('data-data'));
            this.prependDataTableIdToNodeIds(thisId, data);

            this.treemap.loadJSON(data);
            this.treemap.refresh();
        },

        /**
         * TODO
         */
        prependDataTableIdToNodeIds: function (prefix, data) {
            data.id = prefix + '-' + data.id;

            var children = data.children || [];
            for (var i = 0; i != children.length; ++i) {
                this.prependDataTableIdToNodeIds(prefix, children[i]);
            }
        }
    });

}(jQuery, window.$jit));