// handle the post data
var submit_form_evento = function(object, event) {
  event.preventDefault();
  event.stopPropagation();

  populate_data_lineup();

  // the ajax post
  $(form).submit();

  return false;
}

function add_option_lineup() {
  var _template = '<div class="nombre"><input name="lineup[]" placeholder="NOMBRE" /></div>';
  $('#jr_evento_lineup').append($(_template));
}

function populate_data_lineup(){
  
}

function event_hide_errors(containerId){
  $.each( $('#'+containerId).find('input'), function( key, value ) {
    $(value).keydown(function(e){
      if($(this).hasClass('field-error')){
        $(this).removeClass('field-error');
        $(this).popover('destroy')
      }
    });
  });
}
