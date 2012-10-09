
/**
 * Functions for Timeline
 */

$(document).ready( function() {
  
  // get results
  $('#timeline-submit').live('click', function(event) {
    event.preventDefault();
    $('#timeline-throbber').removeClass('hidden');
    $('#timeline-results').html('');
    
    var members = [];
    $("input:hidden[name='members[]']").each( function() {
      members.push($(this).val());
    });
     
    elgg.get('ajax/view/au_analytics/results/timeline', {
      data: {
        types: $('#timeline-types').val(),
        subtypes: $('#timeline-subtypes').val(),
        group: $('#timeline-group').val(),
        cumulative: $('#timeline-cumulative').val(),
        interval: $('#timeline-interval').val(),
        display: $('#timeline-display').val(),
        access: $('#timeline-access').val(),
        time_upper: $('input:hidden[name=created_time_upper]').val(),
        time_lower: $('input:hidden[name=created_time_lower]').val(),
        members: members
      },
      success: function(result, success, xhr){
        $('#timeline-results').html(result);
        $('#timeline-throbber').addClass('hidden');
      }
    })
  });
});