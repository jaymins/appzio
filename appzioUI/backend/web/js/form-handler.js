(function ($) {

    $(document).ready(function () {
        'use strict';

        initDateRange();

        $(document).on('pjax:complete', function () {
            initDateRange();
        });

        $('.users-edit-form').on('submit', function (e) {

            e.preventDefault();

            let $form = $(this),
                csrfToken = $('meta[name="csrf-token"]').attr('content');

            let args = {
                'field_to_update': $form.find('input[name="current_var"]').val(),
                'value_to_update': $form.find('.form-control').val(),
                '_p': $form.find('input[name="_p"]').val(),
                '_csrf-backend': csrfToken,
            };

            handleAjaxForms($form, args);

        });

        // Tickers

        $('#exchange-select').on('change', function () {
            let $select = $(this),
                selectedItem = $select.val(),
                selectedItemText = $select.find(':selected').text(),
                $tickers = $('select#tickers-select'),
                ajaxURL = $select.data('url');

            /*let matchIndex = selectedItemText.indexOf(' ('),
                textTrimmed = selectedItemText.substring(0, matchIndex !== -1 ? matchIndex : selectedItemText.length);

            $( '#exchange-name' ).val( textTrimmed );*/

            $.ajax({
                url: ajaxURL,
                type: 'GET',
                dataType: 'JSON',
                data: {
                    'exchange_id': selectedItem
                },
                beforeSend: function (jqXHR, settings) {
                    $tickers
                        .attr('disabled', true)
                        .addClass('select-loading');
                },
                success: function (data) {

                    if ($.isEmptyObject(data.output)) {
                        return false;
                    }

                    let items = '';

                    $.each(data.output, function (key, value) {
                        items += '<option data-exchange="' + value.exchange + '" data-currency="' + value.currency + '" value="' + key + '">' + value.name + '</option>';
                    });

                    $tickers
                        .html(items)
                        .attr('disabled', false)
                        .removeClass('select-loading');

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error: ' + errorThrown);
                }
            });

        });

        $('#tickers-select').on('change', function () {
            let selectedItem = $('#tickers-select option:selected').text(),
                selectedExchange = $('#tickers-select option:selected').data('exchange'),
                selectedCurrency = $('#tickers-select option:selected').data('currency');

            if (selectedItem) {
                $('#ticker-company').val(selectedItem);
                $('#exchange-name').val(selectedExchange);
                $('#exchange-currency').val(selectedCurrency);
            }
        });

        // Initiate all repeaters
        initRepeaters();

        $(document).on('click', '.show-hide, .offset-link', function (e) {
            e.preventDefault();

            let $parentBox = $(this).parent('.box-header').siblings('.box-body');

            $parentBox.slideToggle(50);
        });

    });

    function initRepeaters() {
        const dragAndDrop = $('.outer-repeater .outer').sortable({
            axis: 'y',
            cursor: 'pointer',
            opacity: 0.8,
            delay: 150,
            handle: '.box-header',
            // update: function(event, ui) {
            //     window.outerRepeater.repeater( 'setIndexes' );
            // }
        });

        $('.outer-repeater').each(function() {

            let prefillVar  = $(this).data('prefill-variable');

            let repeater = $(this).repeater({
                initEmpty: true, // TODO: this should be probably configurable using a data variable
                isFirstItemUndeletable: true,
                defaultValues: {'field-input': ''},
                show: function () {

                    let $this = $(this),
                        currentFieldValue = $this.find('.select2').val(),
                        currentFieldType = $this.find('.select2 option:selected').text(),
                        innerElementsCount = $this.find('.inner-repeater-body > .inner').length;

                    let $heading = $this.find('h3.box-title'),
                        headingPlaceholder = $heading.data('field-placeholder'),
                        headingPrefix = $heading.data('field-prefix');

                    if (headingPlaceholder !== '') {
                        let inputElement = $this.find('.' + headingPlaceholder),
                            inputText = inputElement.val();

                        if (inputText !== '') {
                            let text = '';
                            let type = inputElement.prop('nodeName');

                            if (type === 'SELECT')
                                inputText = inputElement.find(':selected').text();

                            if (headingPrefix !== '') {
                                text = headingPrefix + ': ';
                            }

                            text += inputText;
                            $heading.text(text);
                        }
                    }

                    showFieldToolbox($this, currentFieldValue);

                    // $this.find( '.inner-repeater .box-title' ).text( 'Total items ' + innerElementsCount )

                    $('.form-control.outer.select2').on('change', function () {
                        showFieldToolbox($this, $(this).val());
                    });

                    let headingText = $this.find('.article-type').text();

                    // Auto-show the new elements
                    if (headingText === 'Block') {
                        $this
                            .find('.box-body.outer-body')
                            .show();
                    }

                    $(this).slideDown();
                },
                hide: function (deleteElement) {
                    // console.log($(deleteElement));

                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                    }

                },
                ready: function (setIndexes) {
                    let $firstElement = $('.outer-repeater .outer'),
                        selectedElementType = $firstElement.find('.form-control.outer').val();

                    $('.form-control.outer.select2').on('change', function () {
                        showFieldToolbox($firstElement, $(this).val());
                    });

                    showFieldToolbox($firstElement, selectedElementType);

                    dragAndDrop.on('drop', setIndexes);
                },
                repeaters: [{
                    isFirstItemUndeletable: false,
                    selector: '.inner-repeater',
                    defaultValues: {'field-input-inner': ''},
                    show: function () {
                        $(this).slideDown();
                    },
                    hide: function (deleteElement) {
                        $(this).slideUp(deleteElement);
                    },
                    ready: function (setIndexes) {
                        // dragAndDrop.on('drop', setIndexes);
                    }
                }]
            });

            // Fill-in the repeaters
            if (typeof(window[prefillVar]) !== 'undefined') {
                repeater.setList(window[prefillVar]);
            }
        });

    }

    function showFieldToolbox($container, fieldClass) {

        let holder = '.group-' + fieldClass;

        if (fieldClass == 'richtext' || fieldClass == 'wraprow') {
            holder = '.group-repeaters';
        }

        $container.find('.settings-group').hide();
        $container.find(holder).show();
    }

    function initDateRange() {

        // Date range picker
        $('#datefilter').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear'
            }
        });

        $('#datefilter').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
            $('#w1').yiiGridView('applyFilter');
        });

        $('#datefilter').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));

            // Really important
            $('#w1').yiiGridView('applyFilter');
        });

    }

    function handleAjaxForms($form, args) {

        let $infoHolder = $form.find('.login-message');

        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            dataType: 'json',
            data: args,
            beforeSend: function (jqXHR, settings) {
            },
            success: function (data) {

                var redirectURL = data.redirect;

                $form
                    .parent('.box')
                    .removeClass('box-primary')
                    .addClass('box-success');

                setTimeout(function () {
                    window.location.href = redirectURL;
                }, 500);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                $infoHolder
                    .html('<p>There was an unexpected error</p>')
                    .fadeIn(400);
            }
        });

    }

})(window.jQuery);