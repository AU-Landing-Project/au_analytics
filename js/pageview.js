
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
    });
  });
  
  
  // deal with pagination
  $('.au_analytics-pagination a').live('click', function(event) {
    event.preventDefault();
    
    var offset = $(this).attr('href').replace(elgg.get_site_url()+'?offset=', '');
    var pageview_url = $('.au_analytics-pagination #au_pagination_pageview_url').val();
    var time_upper = $('.au_analytics-pagination #au_pagination_time_upper').val();
    var time_lower = $('.au_analytics-pagination #au_pagination_time_lower').val();
    var viewtype = $('.au_analytics-pagination #au_pagination_viewtype').val();
    
    var members = [];
    $(".au_analytics-pagination input:hidden[name='au_pagination_members[]']").each( function() {
      members.push($(this).val());
    });
    
    $('#pageview-throbber').removeClass('hidden');
    $('#pageview-results').html('');
    
    elgg.get('ajax/view/au_analytics/results/pageview', {
      data: {
        viewtype: viewtype,
        pageview_url: pageview_url,
        time_upper: time_upper,
        time_lower: time_lower,
        members: members,
        offset: offset
      },
      success: function(result, success, xhr){
        $('#pageview-results').html(result);
        $('#pageview-throbber').addClass('hidden');
      }
    });
    
  });
});