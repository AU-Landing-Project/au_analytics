
/**
 * Functions for pageview
 */

$(document).ready( function() {
  
  // get results
  $('#pageview-submit').live('click', function(event) {
    event.preventDefault();
    $('#pageview-throbber').removeClass('hidden');
    $('#pageview-results').html('');
    
    var members = [];
    $("input:hidden[name='members[]']").each( function() {
      members.push($(this).val());
    });
     
    elgg.get('ajax/view/au_analytics/results/pageview', {
      data: {
        viewtype: $('#pageview-viewtype').val(),
        pageview_url: $('#pageview-url').val(),
        time_upper: $('input:hidden[name=created_time_upper]').val(),
        time_lower: $('input:hidden[name=created_time_lower]').val(),
        members: members
      },
      success: function(result, success, xhr){
        $('#pageview-results').html(result);
        $('#pageview-throbber').addClass('hidden');
      }
    })
  });
});