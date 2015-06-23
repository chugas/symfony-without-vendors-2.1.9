// handle the post data
var submit_form_productora = function(object, event) {
  event.preventDefault();
  event.stopPropagation();

  if(!validate_name_productora()){
    add_errors_productora('El nombre no puede ser vacio');
    return false;
  }

  var form = $('#jr_form_productora');
  var url  = $(form).attr('action');
  var type = $(form).attr('method');
  var data = $(form).serialize();

  init_load_productora(object);
        
  // the ajax post
  $.ajax({
    url: url,
    type: type,
    data: data,
    success: function(response) {
      var id = response.id;
      var text = response.text;
      update_container_productora(id, text);
      close_modal_productora();

      return;
    },
    error: function(response){
      add_errors_productora(response.error);
    },
    complete: function(response){
      hide_load_productora(object);
    }
  });

  return false;
}

function close_modal_productora() {
  $.fancybox.close();
}

function update_container_productora(id, text) {
  $('#success_evento_productora').append($('<option>', {'value': id, 'text': text }));
  $("#success_evento_productora option:selected").prop("selected", false);
  $('#success_evento_productora option[value="'+id+'"]').prop('selected', true);
  $('#success_evento_productora').select2("val", id);
}

function init_load_productora(object){
  $(object).attr('disabled', 'disabled');
  $(object).find('i').attr('class','icon-spinner icon-spin');
}

function hide_load_productora(object){
  $(object).removeAttr('disabled');
  $(object).find('i').attr('class','icon-envelope');  
}

function add_errors_productora(msg) {
  var message = $('<ul><li>' + msg +'</li></ul>');

  var target = $('#success_productora_form_type_name');

  target.popover({
      content: message,
      trigger: 'hover',
      html: true,
      placement: 'right',
      template: '<div class="popover"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"><p></p></div></div></div>'
  });

  $(target).addClass('field-error');

  event_hide_errors('jr_form_productora');

  return false;
}

function validate_name_productora(){
  var input = $('#success_productora_form_type_name');

  if ($(input).val() != "") {
    return true;
  }

  return false;
}
