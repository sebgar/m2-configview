define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/confirm',
    "Magento_Ui/js/modal/alert"
], function ($, jqueryUi, modalConfirm, modalAlert) {
    'use strict';

    $.widget('mage.configview_system', {
        options: {},

        _create: function ()
        {
            this.loadRewrites();
            this.bindEvents();
        },

        bindEvents: function()
        {
            // close popin
            $(this.options.elements.popin_close).on('click', function(){
                $(this.options.elements.popin).hide();
            }.bind(this));
        },

        getElementKey: function(element)
        {
            var keys = [];
            var regex = /groups\[([a-zA-Z0-9_]+)\]\[fields\]\[([a-zA-Z0-9_]+)\]\[value\]/;
            var found = element.attr('name').match(regex);
            if (found !== null && found.length === 3) {
                if (element.attr('id')) {
                    var section = element.attr('id').replace('_'+found[1]+'_'+found[2], '');
                    keys.push(section);
                    keys.push(found[1]);
                    keys.push(found[2]);
                }
            }

            return keys;
        },

        loadRewrites: function()
        {
            var keys = [];
            $(this.options.elements.form_elements).each(function(index, element) {
                var keyParts = this.getElementKey($(element));
                if (keyParts.length === 3) {
                    keys.push(keyParts.join('/'));
                }
            }.bind(this));

            if (keys.length > 0) {
                $.ajax({
                    url: this.options.urls.load_rewrite,
                    method: "POST",
                    data: {
                        keys: keys.join(',')
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

        createLinks: function(rewrites)
        {
            $(this.options.elements.form_elements).each(function(index, element) {
                var keyParts = this.getElementKey($(element));
                if (keyParts.length === 3) {
                    var key = keyParts.join('/');
                    var nb = typeof rewrites[key] !== 'undefined' ? rewrites[key] : 0;
                    $(element).parent().append('<a href="javascript:void(0)" data-section="'+keyParts[0]+'" data-group="'+keyParts[1]+'" data-field="'+keyParts[2]+'" class="configview-link">'+nb+' rewrite</a>');
                }
            }.bind(this));

            // load config
            $('.configview-link').on('click', function(){
                var element = $(event.currentTarget);
                this.loadPopin(element.data('section'), element.data('group'), element.data('field'));
            }.bind(this));
        },

        loadPopin: function(section, group, field)
        {
            if (section !== '' && group !== '' && field !== '') {
                // empty container
                $(this.options.elements.popin_container).html('');

                // show popin
                $(this.options.elements.popin).show();

                // call request
                $.ajax({
                    url: this.options.urls.load_popin,
                    method: "POST",
                    data: {
                        section: section,
                        group: group,
                        field: field
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

    return $.mage.configview_system;
});
