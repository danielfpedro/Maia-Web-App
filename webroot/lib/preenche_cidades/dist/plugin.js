;
(function($, window, document, undefined) {

    var pluginName = 'preencheCidades';
    var defaults = {
        targetDisabledText: null,
        targetBlankText: null,
        targetLoadingText: 'Carregando...',
        value: 'id',
        label: 'name',
        key: null,
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
        done: function() {

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
                _this.options.done();
            }

            $(this.element).change(function() {
                var $this = $(this);

                if ($this.val()) {
                    console.log('Select do estado possui valor');
                    console.log('Executar loadCities()');
                    _this.loadCities();
                } else {
                    _this.options.onCitiesReseted();
                    _this.resetCitiesElement();
                }
            });
        },
        loadCities: function() {
            console.log('Executando loadCities()');
            var _this = this;

            console.log("Executar setLoadingState()");
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
                var dataResult = (!_this.options.key) ? data : data[_this.options.key];
                console.log('Data eita', data);
                console.log('Data Result', dataResult);
                console.log('Chama on load success');
                _this.options.onLoadSuccess(dataResult);
                _this.options.done();
                dfd.resolve(dataResult);
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
            console.log('Executando setLoadingState()');
            console.log("Alterando do texto do objecto da cidade", this.options.targetSelector);
            $(this.options.targetSelector)
                .empty()
                .append('<option value="">'+this.options.targetLoadingText+'</option>')
                .attr('readonly', true);
        },
        createCitiesOptions: function(data) {
            var _this = this;

            var preselectedCityId = $(this.element).attr('data-preselect-city');

            console.log('Valr de preselect city', $(this.element));

            if (!this.options._createOptions) {

                $.each(data, function(index, value) {
                    if (value[_this.options.value] == preselectedCityId) {
                        console.log('Bateu em ', value);
                        console.log('Done?', $(_this.element).data('preselect-city-done'));
                    }
                    $(_this.options.targetSelector)
                        .append('<option value="'+value[_this.options.value]+'" '+((value[_this.options.value] == preselectedCityId && !$(_this.element).data('preselect-city-done')) ? 'selected' : '') +'>'+value[_this.options.label]+'</option>')
                });

            } else {
                var options = this.options._createOptions(data);
                $(_this.options.targetSelector).append(options);
            }

            this.options.afterCreateOptions();

            $(this.element).data('preselect-city-done', false);

            $(_this.options.targetSelector).attr('readonly', false);
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
                .attr('readonly', true);
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
