;
(function($, window, document, undefined) {

    var pluginName = 'preencheCidades';
    var defaults = {
        targetDisabledText: null,
        targetBlankText: null,
        targetLoadingText: 'Carregando...',
        value: 'id',
        label: 'name',
        targetBlankOption: false,
        extraData: function() {
            return {};
        },
        beforeLoad: function() {

        },
        afterLoad: function() {

        },
        onLoadSuccess: function(response) {

        },
        onLoadError: function(jqXHR, textStatus, error) {

        },
        afterCreateOptions: function() {

        },
        onCitiesReseted: function() {

        },
        _createOptions: null
    };

    function Plugin(element, options) {
        this.element = element;

        this.options = $.extend({}, defaults, options);

        this.currentJsonRequest = null;

        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {
        init: function(name) {
            var _this = this;

            this.resetCitiesElement();

            if ($(this.element).val()) {
                _this.loadCities();
            } else {
                _this.resetCitiesElement();
            }

            $(this.element).change(function() {
                var $this = $(this);

                if ($this.val()) {
                    _this.loadCities();
                } else {
                    _this.options.onCitiesReseted();
                    _this.resetCitiesElement();
                }
            });
        },
        loadCities: function() {
            var _this = this;

            _this.setLoadingState();

            $.when(_this.getData()).then(function(data){
                _this.cleanCities();
                _this.createCitiesOptions(data);
            });
        },
        getData: function() {
            var _this = this;

            var dfd = $.Deferred();
            var value = $(this.element).val();

            var dataToSend = $.extend({}, {value: value}, this.options.extraData());

            if (this.currentJsonRequest) {
                this.currentJsonRequest.abort();
            }

            this.options.beforeLoad.call();
            this.currentJsonRequest = $.getJSON(this.options.source, dataToSend, function(data){
                _this.options.onLoadSuccess(data);
                dfd.resolve(data);
            })
            .fail(function(jqXHR, textStatus, error) {
                _this.options.onLoadError(jqXHR, textStatus, error);
            })
            .always(function() {
                _this.options.afterLoad();
            });

            return dfd.promise();
        },
        setLoadingState: function() {
            $(this.options.targetSelector)
                .empty()
                .append('<option value="">'+this.options.targetLoadingText+'</option>')
                .attr('disabled', true);
        },
        createCitiesOptions: function(data) {
            var _this = this;

            var preselectedCityId = $(this.element).data('preselect-city');

            if (!this.options._createOptions) {

                $.each(data, function(index, value) {
                    $(_this.options.targetSelector)
                        .append('<option value="'+value[_this.options.value]+'" '+((value[_this.options.value] == preselectedCityId && !$(_this.element).data('preselect-city-done')) ? 'selected' : '') +'>'+value[_this.options.label]+'dadas</option>')
                });

            } else {
                var options = this.options._createOptions(data);
                $(_this.options.targetSelector).append(options);
            }

            this.options.afterCreateOptions();

            $(this.element).data('preselect-city-done', true);

            $(_this.options.targetSelector).attr('disabled', false);
        },
        cleanCities: function () {
            $(this.options.targetSelector).empty();

            if (this.options.targetBlankOption) {
                $(this.options.targetSelector)
                    .append('<option value="">'+this.options.targetBlankText+'</option>');
            }
        },
        resetCitiesElement: function() {
            $(this.options.targetSelector)
                .empty()
                .append('<option value="">'+this.options.targetDisabledText+'</option>')
                .attr('disabled', true);
        }
    }


    $.fn[pluginName] = function(options) {
        return this.each(function() {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
            }
            else if ($.isFunction(Plugin.prototype[options])) {
                $.data(this, 'plugin_' + pluginName)[options]();
            }
        });
    }


})(jQuery, window, document);
