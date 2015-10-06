define(['require', 'jquery', 'elgg'], function(require, $, elgg) {

    require([
        'au_analytics/jqplot/highlighter',
        'au_analytics/jqplot/cursor',
        'au_analytics/jqplot/dateaxis',
        'au_analytics/jqplot/barRender',
        'au_analytics/jqplot/categoryAxis',
        'au_analytics/jqplot/pointLabels',
        'au_analytics/jqplot/canvasAxisLabel',
        'au_analytics/jqplot/canvasText',
        'au_analytics/tablesorter',
        'au_analytics/tablesorter/pager'
    ]);
    
    // get results
    $(document).on('click', '#timeline-submit', function(event) {
        event.preventDefault();
        $('#timeline-throbber').removeClass('hidden');
        $('#timeline-results').html('');

        var members = [];
        $("input:hidden[name='members[]']").each(function() {
            members.push($(this).val());
        });

        var types = '';
        if ($('#timeline-types').val()) {
            types = $('#timeline-types').val();
        }

        var subtypes = '';
        if ($('#timeline-subtypes').val()) {
            subtypes = $('#timeline-subtypes').val();
        }

        var annotations = '';
        if ($('#timeline-annotations').val()) {
            annotations = $('#timeline-annotations').val();
        }

        elgg.get('ajax/view/au_analytics/results/timeline', {
            timeout: 600000, //10 min
            data: {
                types: types,
                subtypes: subtypes,
                annotations: annotations,
                group: $('#timeline-group').val(),
                cumulative: $('#timeline-cumulative').val(),
                interval: $('#timeline-interval').val(),
                display: $('#timeline-display').val(),
                access: $('#timeline-access').val(),
                time_upper: $('input:hidden[name=created_time_upper]').val(),
                time_lower: $('input:hidden[name=created_time_lower]').val(),
                members: members
            },
            success: function(result, success, xhr) {
                $('#timeline-results').html(result);
                $('#timeline-throbber').addClass('hidden');
            },
            error: function(result, response, xhr) {
                if (response == 'timeout') {
                    $('#timeline-throbber').addClass('hidden');
                    $('#timeline-results').html(elgg.echo('au_analytics:timeout'));
                }
            }
        });
    });
});
