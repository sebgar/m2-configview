define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/confirm',
    "Magento_Ui/js/modal/alert"
], function ($, jqueryUi, modalConfirm, modalAlert) {
    'use strict';

    $.widget('mage.configview_system', {
        options: {},
        keysFound: [],

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

        getElementKey: function(element, isInherit)
        {
            var keys = [];
            var regex = /groups\[([a-zA-Z0-9_]+)\]\[fields\]\[([a-zA-Z0-9_]+)\]/;
            if (typeof element.attr('name') === 'string') {
                var found = element.attr('name').match(regex);
                if (found !== null && found.length === 3) {
                    if (element.attr('id')) {
                        if (isInherit) {
                            var section = element.attr('id').replace('_'+found[1]+'_'+found[2]+'_inherit', '');
                        } else {
                            var section = element.attr('id').replace('_'+found[1]+'_'+found[2], '');
                        }
                        keys.push(section);
                        keys.push(found[1]);
                        keys.push(found[2]);
                    }
                }
            }

            return keys;
        },

        loadRewrites: function()
        {
            $(this.options.elements.form_elements).each(function(index, element) {
                element = $(element);
                var keyParts = [];

                // has inherit
                var inheritElements = element.parent().find(this.options.elements.inherit_field);
                if (inheritElements.length > 0) {
                    keyParts = this.getElementKey($(inheritElements[0]), true);
                } else {
                    var inputFields = element.find('input, select, textarea');
                    if (inputFields.length > 0) {
                        keyParts = this.getElementKey($(inputFields[0]), false);
                    }
                }

                if (keyParts.length === 3) {
                    this.keysFound.push(keyParts.join('/'));
                }
            }.bind(this));

            if (this.keysFound.length > 0) {
                $.ajax({
                    url: this.options.urls.load_rewrite,
                    method: "POST",
                    data: {
                        keys: this.keysFound.join(',')
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
            $(this.keysFound).each(function(index, key) {
                var nb = typeof rewrites[key] !== 'undefined' ? rewrites[key] : 0;
                var keyParts = key.split('/');
                var link = '<a href="javascript:void(0)" data-section="'+keyParts[0]+'" data-group="'+keyParts[1]+'" data-field="'+keyParts[2]+'" class="configview-link">'+nb+' rewrite</a>';

                if ($('#row_'+keyParts.join('_').length > 0)) {
                    var tdUseDefault = $('#row_'+keyParts.join('_')).find('td.use-default');
                    if (tdUseDefault.length > 0) {
                        tdUseDefault.append(link)
                    } else {
                        var tdLast = $('#row_'+keyParts.join('_')).find('td:last-child');
                        if (tdLast.length > 0) {
                            tdLast.append(link)
                        }
                    }
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
