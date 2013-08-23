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

            this.thisId = domElem.attr('id');
            var treemapContainerId = this.thisId + '-infoviz-treemap';
            var treemapContainer = $('.infoviz-treemap', domElem).attr('id', treemapContainerId);

            var self = this;
            this.treemap = new $jit.TM.Squarified({
                injectInto: treemapContainerId,
                titleHeight: 15,
                animate: true, // TODO: disable on ipad/w/o native canvas support
                offset: 1,
                levelsToShow: 2,
                constrained: true,
                Events: {
                    enable: true,
                    onClick: function (node) {
                        self._onLeftClickNode(node);
                    },
                    onRightClick: function (node) {
                        self._onRightClickNode(node);
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

            this.data = JSON.parse(treemapContainer.attr('data-data'));
            this._prependDataTableIdToNodeIds(this.thisId, this.data);
            this._setTreemapColors(this.data);

            this.treemap.loadJSON(this.data);
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

        /**
         * TODO
         */
        _onLeftClickNode: function (node) {
            if (!node) {
                return;
            }

            if (this._isOthersNode(node)) {
                this._enterOthersNode(node);
            } else if (this._nodeHasSubtable(node)) {
                this._enterSubtable(node);
            }
        },

        /**
         * TODO
         */
        _enterOthersNode: function (node) {
            if (!node.data.loaded) {
                var self = this;
                this._loadOthersNodeChildren(node, function (newNode) {
                    self.treemap.enter(newNode);
                });
            } else {
                this.treemap.enter(node);
            }
        },

        /**
         * TODO
         */
        _enterSubtable: function (node) {
            if (!node.data.loaded) {
                var self = this;
                this._loadSubtableNodeChildren(node, function (newNode) {
                    self.treemap.enter(newNode);
                });
            } else {
                this.treemap.enter(node);
            }
        },

        /**
         * TODO
         */
        _loadOthersNodeChildren: function (node, callback) {
            var ajax = this._getNodeChildrenAjax({filter_offset: node.data.aggregate_offset}, node, callback);
            ajax.send();
        },

        /**
         * TODO
         */
        _loadSubtableNodeChildren: function (node, callback) {
            var ajax = this._getNodeChildrenAjax({idSubtable: node.data.idSubtable}, node, callback);
            ajax.send();
        },

        /**
         * TODO
         */
        _getNodeChildrenAjax: function (overrideParams, node, callback) {
            var self = this,
                dataNode = this._findNodeWithId(node.id),
                params = $.extend({}, this.param, overrideParams, {
                    module: 'API',
                    method: 'TreemapVisualization.getTreemapData',
                    action: 'index',
                    apiMethod: this.param.module + '.' + this.param.action, // TODO: will this work for all subtables?
                    format: 'json',
                    columns: this.param.columns,
                    filter_truncate: this.props.max_graph_elements - 1,
                    filter_limit: -1
                });

            // make sure parallel load data requests aren't made
            node.data.loaded = dataNode.data.loaded = true;

            var ajax = new ajaxHelper();
            ajax.addParams(params, 'get');
            ajax.setCallback(function (response) {
                self._prependDataTableIdToNodeIds(self.thisId, response);
                self._setTreemapColors(response);

                dataNode.children = response.children;
                self.treemap.loadJSON(self.data);

                // refresh the treemap w/o animation
                self.treemap.config.animate = false;
                self.treemap.refresh();
                self.treemap.config.animate = true;

                callback(self.treemap.graph.getNode(node.id));
            });
            ajax.setFormat('json');
            return ajax;
        },

        /**
         * TODO
         */
        _isOthersNode: function (node) {
            return this._getRowIdFromNode(node) == -1;
        },

        /**
         * TODO
         */
        _nodeHasSubtable: function (node) {
            return !! node.data.idSubtable;
        },

        /**
         * TODO
         */
        _getRowIdFromNode: function (node) {
            return node.id.substring(node.id.lastIndexOf('_') + 1);
        },

        /**
         * TODO
         */
        _onRightClickNode: function (node) {
            this.treemap.out();
        },

        _findNodeWithId: function (id, node) {
            if (!node) {
                node = this.data;
            }

            if (node.id == id) {
                return node;
            }

            for (var i = 0; i != node.children.length; ++i) {
                var result = this._findNodeWithId(id, node.children[i]);
                if (result) {
                    return result;
                }
            }
        },
    });

}(jQuery, $jit));