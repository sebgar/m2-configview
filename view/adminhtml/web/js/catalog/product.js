define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/confirm',
    "Magento_Ui/js/modal/alert"
], function ($, jqueryUi, modalConfirm, modalAlert) {
    'use strict';

    $.widget('mage.configview_catalog_product', {
        options: {},

        _create: function ()
        {
            $(this.options.elements.btn).on('click', function(){
                this.createRewrites();
            }.bind(this));
        },

        createRewrites: function ()
        {
            if (typeof this.options.product !== 'undefined' && this.options.product > 0) {
                this.deleteLinks();
                this.loadRewrites();
                this.bindEvents();
            }
        },

        bindEvents: function()
        {
            // close popin
            $(this.options.elements.popin_close).on('click', function(){
                $(this.options.elements.popin).hide();
            }.bind(this));
        },

        getElementCode: function(element)
        {
            var code = '';
            var regex = /product\[([a-zA-Z0-9_]+)\]/;
            var found = element.attr('name').match(regex);
            if (found !== null && found.length === 2) {
                code = found[1];
            }

            return code;
        },

        loadRewrites: function()
        {
            var codes = [];
            $(this.options.elements.form_elements).each(function(index, element) {
                var code = this.getElementCode($(element));
                if (code !== '') {
                    codes.push(code);
                }
            }.bind(this));

            if (codes.length > 0) {
                $.ajax({
                    url: this.options.urls.load_rewrite,
                    method: "POST",
                    data: {
                        attributes: codes.join(','),
                        id: this.options.product
                    }
                })
                .done(function(response) {
                    if (response.error) {
                        modalAlert({
                            title: 'Error',
                            content: response.error,
                            actions: {
                                always: function(){}
                            }
                        });
                    }  else {
                        this.createLinks(response.rewrites);
                    }
                }.bind(this));
            }
        },

        deleteLinks: function()
        {
            $('.configview-link').parent().html('');
        },

        createLinks: function(rewrites)
        {
            $(this.options.elements.form_elements).each(function(index, element) {
                var code = this.getElementCode($(element));
                if (code !== '') {
                    if (typeof rewrites[code]['static'] !== 'undefined' && rewrites[code]['static'] !== 1) {
                        var nb = typeof rewrites[code]['nb'] !== 'undefined' ? rewrites[code]['nb'] : 0;
                        $(element).closest('.admin__field').append('<div class="admin__field-rewrite"><a href="javascript:void(0)" data-code="'+code+'" class="configview-link">'+nb+' rewrite</a></div>');
                    }
                }
            }.bind(this));

            // load config
            $('.configview-link').on('click', function(){
                var element = $(event.currentTarget);
                this.loadPopin(element.data('code'));
            }.bind(this));
        },

        loadPopin: function(attribute)
        {
            if (attribute !== '') {
                // empty container
                $(this.options.elements.popin_container).html('');

                // show popin
                $(this.options.elements.popin).show();

                // call request
                $.ajax({
                    url: this.options.urls.load_popin,
                    method: "POST",
                    data: {
                        attribute: attribute,
                        id: this.options.product
                    }
                })
                .done(function(response) {
                    if (response.error) {
                        modalAlert({
                            title: 'Error',
                            content: response.error,
                            actions: {
                                always: function(){}
                            }
                        });
                    }  else {
                        // update container
                        var container = $(this.options.elements.popin_container);
                        container.html(response.html);
                    }
                }.bind(this));
            }
        }
    });

    return $.mage.configview_catalog_product;
});