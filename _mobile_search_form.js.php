/*jslint browser: true*/
/*global window*/
/*global jQuery*/
(function ($, window) {
    'use strict';

    $(function () {
        var getShorterAirportDescription,
            isIphone,
            isMobileAndroid,
            getModalAutocompleteHash,
            mapAutocompleteResponseData,
            displayActiveAirport,
            cache = {},
            term,
            trackEvent;

        $('body').addClass('mobile-home-page');

        /**
         * @param {string} airport
         * @return string
         */
        getShorterAirportDescription = function (airport) {
            var airport_str = '---', // placeholder
                airport_arr;

            if (!airport) {
                return airport_str;
            }

            airport_arr = airport.split(',');

            if (airport_arr.length > 1) {
                airport_str = airport_arr[0];
            }

            return airport_str;
        };

        /**
         * @return boolean
         */
        isIphone = function () {
            return navigator.userAgent.indexOf('iPhone') !== -1;
        };

        /**
         * @return boolean
         */
        isMobileAndroid = function () {
            return navigator.userAgent.indexOf('Mobile') !== -1 && navigator.userAgent.indexOf('Android') !== -1;
        };

        /**
         * @return string
         */
        getModalAutocompleteHash = function () {
            return '#modalAutocomplete';
        };

        /**
         * @param {Object} data
         * @returns mixed
         */
        mapAutocompleteResponseData = function (data) {
            return $.map(data, function (item) {
                var label = item.code + ' - ' + item.name,
                    value = item.code.replace(/<(\/)?strong>/gi, '') + ' - ' + item.name.replace(/<(\/)?strong>/gi, '');

                return {
                    label: label,
                    value: value,
                    code: item.code,
                    is_city: item.is_city
                };
            });
        };

        /**
         * @param {int} index
         * @param {string} direction
         */
        displayActiveAirport = function (index, direction) {
            var li = '',
                id_prefix = direction == 'from' ? 'leaving-from-' : 'going-to-',
                name,
                code;

            $('.form-field-active-item').remove();

            name = $('[name=seg' + index + '_' + direction + ']').val();
            code = $('[name=seg' + index + '_' + direction + '_code]').val();

            if (!code.length) {
                return;
            }

            // remove duplicate airport
            $('#' + id_prefix + index + ' .recent-search-list li').each(function () {
                if ($(this).text().indexOf(code) !== -1) {
                    $(this).remove();
                }
            });

            li += '<li class="form-field-selected-item form-field-active-item">';
            li += '<a href="#modalAutocomplete" data-code="' + code + '" data-name="' + name + '" data-index="' + index + '" data-direction="' + direction + '">';
            li += '<i class="fa fa-check icon" aria-hidden="true"></i>';
            li += '<div class="airport-name-wrapper">';
            li += '<span class="airport-name-outer">';
            li += '<p class="airport-name-inner">' + name + '</p>';
            li += '</span>';
            li += '</div>';
            li += '</a>';
            li += '</li>';

            $('.search-field-content')
                .filter('[id=' + id_prefix + index + ']')
                .find('.recent-search-list')
                .prepend(li);
        };

        /**
         * @param {string} category
         * @param {string} action
         * @param {string} label
         */
        trackEvent = function (category, action, label) {
            <?= Mv_Ota_Tracker::UNIVERSAL_GA_TRACKER_NAME?>('send', {
                hitType: 'event',
                eventCategory: category,
                eventAction: action,
                eventLabel: label
            });
        };

        $('.form-field-from-to-sort').on('click', function (e) {
            e.preventDefault();

            var tmp_val = $('[name=seg0_from]').val(),
                tmp_val_code = $('[name=seg0_from_code]').val();

            $('[name=seg0_from]').val($('[name=seg0_to]').val());
            $('[name=seg0_from_code]').val($('[name=seg0_to_code]').val());

            $('[name=seg0_to]').val(tmp_val);
            $('[name=seg0_to_code]').val(tmp_val_code);

            return false;
        });

        $('.form-field-submit-button2').on('click', function() {
            // clean up multi-city form values
            var from,
                from_code,
                to,
                to_code,
                i;

            if ($('.inline-search-form').hasClass('form-wrap-multi')) {
                for (i = 0; i < 5; i++) {
                    from = 'seg' + i + '_from';
                    to = 'seg' + i + '_to';
                    from_code = 'seg' + i + '_from_code';
                    to_code = 'seg' + i + '_to_code';

                    if (!$('[name=' + from + ']').is(':visible')) {
                        $('[name=' + from + ']').val('');
                        $('[name=' + from_code + ']').val('');
                        $('[name=' + to + ']').val('');
                        $('[name=' + to_code + ']').val('');
                    }
                }
            }
        });

        $('.airport-field-modal').on('click', function (e) {
            e.preventDefault();

            var name = this.name,
                index = name.replace(/[^\d]/g, ''),
                next_index = 0,
                elem_from,
                elem_from_code,
                elem_to,
                elem_to_code,
                next_elem_from,
                next_elem_from_code;

            if (!isNaN(index)) {
                next_index = parseInt(index) + 1;
            } else {
                return;
            }

            window.location.hash = getModalAutocompleteHash();

            elem_from = $('[name=seg' + index + '_from]');
            elem_from_code = $('[name=seg' + index + '_from_code]');
            elem_to = $('[name=seg' + index + '_to]');
            elem_to_code = $('[name=seg' + index + '_to_code]');

            $('#modal_seg' + index + '_from').html(getShorterAirportDescription(elem_from.val()));
            $('#modal_seg' + index + '_to').html(getShorterAirportDescription(elem_to.val()));

            $('.modal-flight-wrap').hide().filter('[data-index=' + index + ']').show();

            Modal.open({
                html: $('#form-field-modal').html(),
                additionalClasses: 'modal-fullscreen',
                fullscreen: true
            });

            //Set modal ID so we can append autocomplete to the correct container
            $('.container-modal').attr('id', 'mobile_modal_container');

            $('.search-field-tabs.main-search-form').show();

            $('.search-field-content').hide();
            $('.search-field-action').removeClass('active');
            $('.search-direction-indicator').removeClass('to');
            $('.search-field-tabs .recent-search-list').show().find('li').show();

            if (name.indexOf('from') !== -1) {
                $('.search-field-content').filter('[id=leaving-from-' + index + ']').show().attr('style', 'display: -webkit-flex; display: -webkit-box; display: flex;').find('.modal-airport-search').focus();
                $('.search-field-action').filter('[data-anchor=leaving-from-' + index + ']').addClass('active');
                $('.going-to-message').hide();
                $('.leaving-from-message').show();
                $('[data-name=seg' + index + '_from]').val(this.value);
                displayActiveAirport(index, 'from');
            } else if (name.indexOf('to') !== -1) {
                $('.search-field-content').filter('[id=going-to-' + index + ']').show().attr('style', 'display: -webkit-flex; display: -webkit-box; display: flex;').find('.modal-airport-search').focus();
                $('.search-field-action').filter('[data-anchor=going-to-' + index + ']').addClass('active');
                $('.search-direction-indicator').filter('[data-anchor=direction-indicator-' + index + ']').addClass('to');
                $('.going-to-message').show();
                $('.leaving-from-message').hide();
                $('[data-name=seg' + index + '_to]').val(this.value);
                displayActiveAirport(index, 'to');
            }

            $('body').on('click', '.search-field-action', function () {
                var anchor = $(this).data('anchor'),
                    index = $(this).data('index');

                if (!$('#' + anchor).length) {
                    return;
                }

                $('.search-field-action').removeClass('active');
                $('.search-direction-indicator').removeClass('to');

                $(this).addClass('active');

                $('.search-field-content').hide();

                $('.search-field-content').filter('[id=' + anchor + ']').show().attr('style', 'display: -webkit-flex; display: -webkit-box; display: flex;').find('.modal-airport-search').focus();

                $('[data-name=seg' + index + '_from]').val('');
                $('[data-name=seg' + index + '_to]').val('');
                if (anchor.indexOf('from') !== -1) {
                    $('[data-name=seg' + index + '_from]').val($('[name=seg' + index + '_from]').val());
                    $('.form-field-from-selected-item a').html($('[name=seg' + index + '_from]').val());
                    $('.going-to-message').hide();
                    $('.leaving-from-message').show();

                    displayActiveAirport(index, 'from');
                } else if (anchor.indexOf('to') !== -1) {
                    $('[data-name=seg' + index + '_to]').val($('[name=seg' + index + '_to]').val());
                    $('.form-field-to-selected-item a').html($('[name=seg' + index + '_to]').val());
                    $('.search-direction-indicator').filter('[data-anchor=direction-indicator-' + index + ']').addClass('to');
                    $('.going-to-message').show();
                    $('.leaving-from-message').hide();

                    displayActiveAirport(index, 'to');
                }
                //Show recent search list
                $('.search-field-tabs .recent-search-list').show().find('li').show();

                //Get length of field and fix cursor position
                var elementToFocus =  $('.search-field-content').filter('[id=' + anchor + ']').find('.modal-airport-search');
               
                elementToFocus.on("focus", function() {
                    this.setSelectionRange(this.value.length, this.value.length);
                    this.value = this.value;
                });

                elementToFocus[0].focus();
            });

            $('body').on('click', '.form-field-selected-item a', function (e) {
                e.preventDefault();

                var idx = $(this).data('index'),
                    airport_code = $(this).data('code'),
                    airport_name = $(this).data('name');

                if ($(this).data('direction') == 'from') {
                    $('.search-airport-value')
                        .filter('[id=modal_seg' + idx + '_from]')
                        .html(getShorterAirportDescription(airport_name));

                    $('[name=seg' + idx + '_from]').val(airport_name);
                    $('[name=seg' + idx + '_from_code]').val(airport_code);

                    $('[data-anchor="going-to-' + idx + '"]').trigger('click');

                    $('.going-to-message').hide();
                    $('.leaving-from-message').show();

                    displayActiveAirport(idx, 'to');
                } else if ($(this).data('direction') == 'to') {
                    Modal.close();
                    $('.search-airport-value')
                        .filter('[id=modal_seg' + idx + '_to]')
                        .html(getShorterAirportDescription(airport_name));

                    $('[name=seg' + idx + '_to]').val(airport_name);
                    $('[name=seg' + idx + '_to_code]').val(airport_code);

                    $('.going-to-message').show();
                    $('.leaving-from-message').hide();

                    if ($('.inline-search-form').hasClass('form-wrap-multi') && idx > 0) {
                        $('[name=seg' + idx + '_date]').prev().trigger('click');
                    } else {
                        $('.mobile-calendar-toggle').trigger('click');
                    }

                    $('.date-picker-action-buttons .reset-btn').focus();
                }

                return false;
            });

            $('.recent-search-list').on('click', '.form-field-selected-item.form-field-active-item a', function (e) {
                var airport_name = $(this).data('name');

                if (airport_name.length) {
                    trackEvent('Search Suggestion', 'Pre-filled', airport_name);
                }
            });

            $('.recent-search-list').on('click', '.form-field-selected-item.recent a', function (e) {
                var airport_name = $(this).data('name');

                if (airport_name.length) {
                    trackEvent('Search Suggestion', 'Recent', airport_name);
                }
            });

            $('.recent-search-list').on('click', '.form-field-selected-item.nearby a', function (e) {
                var airport_name = $(this).data('name');

                if (airport_name.length) {
                    trackEvent('Search Suggestion', 'Location', airport_name);
                }
            });

            $('.modal-airport-search').on('keyup', function (e) {
                var val,
                    text,
                    html,
                    search,
                    i,
                    span;

                if (e.target) {
                    val = e.target.value;

                    if (val.length < 3) {
                        $('.search-field-tabs .recent-search-list').show();
                        $('.search-field-tabs .recent-search-list li').show().each(function () {
                            text = $(this).find('span').text();
                            if (text.toLowerCase().indexOf(val.toLowerCase()) !== -1) {
                                html = $(this).html();
                                span = $(this).find('span').html();
                                search = text.match(new RegExp(val, 'gi'));

                                for (i = 0; i < search.length; i++) {
                                    span = text.replace(search[i], '<em>' + search[i] + '</em>');
                                }

                                $(this).find('span').html(span);
                            } else {
                                $(this).hide();
                            }
                        });
                    } else {
                        $('.search-field-tabs .recent-search-list').hide();
                    }
                }
            }).autocomplete({
                minLength: 3,
                source: function (request, response) {
                    term = request.term;

                    if (cache.hasOwnProperty(term)) {
                        response(mapAutocompleteResponseData(cache[term]));
                        return;
                    }

                    $.ajax({
                        url: '<?= $this->getBaseUrl() ?>airports/mobile-autocomplete',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            'max_results': 15,
                            term: request.term,
                            direction: 'departure',
                            show_search_counts: 1
                        },
                        success: function (data) {
                            cache[term] = data;

                            response(mapAutocompleteResponseData(data));
                        }
                    });

                },
                select: function (event, ui) {
                    name = $(event.target).data('name');

                    // pull out the <strong> tags for the code input
                    var newCodeValue = ui.item.code.replace(/<(\/)?strong>/gi, '');

                    if (name.indexOf('from') !== -1) {
                        $('[data-anchor="going-to-' + index + '"]').trigger('click');
                    } else if (name.indexOf('to') !== -1) {
                        Modal.close();
                        if ($('.inline-search-form').hasClass('form-wrap-multi') && index > 0) {
                            $('[name=seg' + index + '_date]').prev().trigger('click');
                        } else {
                            $('.mobile-calendar-toggle').trigger('click');
                        }

                        $('.date-picker-action-buttons .reset-btn').focus();
                    }

                    if (name.indexOf('from') !== -1) {
                        elem_from.val(ui.item.value);
                        elem_from_code.val(newCodeValue);
                        $('.search-airport-value').filter('[id=modal_seg' + index + '_from]').html(getShorterAirportDescription(elem_from.val()));
                        $('#leaving-from-' + index + ' .form-field-from-selected-item a').html(elem_from.value);
                    } else if (name.indexOf('to') !== -1) {
                        elem_to.val(ui.item.value);
                        elem_to_code.val(newCodeValue);
                        $('.search-airport-value').filter('[id=modal_seg' + index + '_to]').html(getShorterAirportDescription(elem_to.val()));
                        $('#going-to-' + index + ' .form-field-to-selected-item a').html(elem_to.value);
                    }

                    if ($('.inline-search-form').hasClass('form-wrap-multi') && name.indexOf('to') !== -1) {
                        next_elem_from = $('[name=seg' + next_index + '_from]');
                        next_elem_from_code = $('[name=seg' + next_index + '_from_code]');

                        if (next_elem_from.length && next_elem_from_code.length) {
                            $('[name=seg' + next_index + '_from]').val(ui.item.value);
                            $('[name=seg' + next_index + '_from_code]').val(newCodeValue);
                        }
                    }

                    if (term.length) {
                        trackEvent('Search Suggestion', 'Autocomplete ('+ term.toUpperCase() +')', ui.item.value);
                    }
                },
                focus :  function (event, ui) {
                    if (isIphone()) {
                        $(event.toElement).trigger('click');
                    }
                }
            });

            $.ui.autocomplete.prototype._renderItem = function (ul, item) {
                var link,
                    inner_content,
                    inner_content_icon,
                    inner_content_label,
                    outer;

                link = $('<a>').addClass('modal-suggestion-link');
                inner_content_icon = '<i class="fa fa-map-marker icon"></i>';
                outer = '<span class="airport-name-outer"><p class="airport-name-inner">' + item.label + '</p></span>';
                inner_content_label = '<div class="airport-name-wrapper">' + outer + '</div>';
                inner_content = inner_content_icon + inner_content_label;
                link.html(inner_content);

                return $('<li>')
                    .addClass('modal-suggestion')
                    .data('item.autocomplete', item)
                    .append(link)
                    .appendTo(ul);
            };

            return false;
        });

        window.onpopstate = function (event) {
            var hash;

            if (isMobileAndroid()) {
                if (event.target && event.target.location) {
                    hash = event.target.location.hash;

                    if (hash !== getModalAutocompleteHash()) {
                        Modal.close();
                        $('.topCloseBtn').trigger('click');
                    }
                }
            }
        };
    });
})(jQuery, window);