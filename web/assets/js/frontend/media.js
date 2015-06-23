var _images_values = {};
var _videos_values = {};

//######################################################################
//################################# IMAGENES ###########################
//######################################################################
function add_image(data) { 
  if(data.result.files.length > 0){
    _images_values[data.result.files[0].media] = data.result.files[0].media;
  }
}

function update_images(){
  var dataContainer = $('#success_medias_values');
  var prototype = $(dataContainer).data('prototype');
  dataContainer.html('');

  var i = 0;
  for ( var value in _images_values ){
    var item = prototype.replace(/__name__/g, i);
    var input = $(item).clone(true);
    $(input).val(value);
    dataContainer.append(input);
    i++;
  }
}

function delete_image(data){
  var value = $(data.context.context).data('value');
  delete _images_values[value];  
}

//######################################################################
//################################# VIDEOS #############################
//######################################################################
function add_video(data) {
  console.log(data);
  _videos_values[data.media] = data.media;
}

function update_videos(){
  var dataContainer = $('#success_videos_values');
  var prototype = $(dataContainer).data('prototype');
  dataContainer.html('');

  var i = 0;
  for ( var value in _videos_values ){
    var item = prototype.replace(/__name__/g, i);    
    var input = $(item).clone(true);
    $(input).val(value);
console.log(dataContainer);
console.log(input);
    dataContainer.append(input);    
    i++;
  }  
}

function delete_video(data){
  var value = $(data.context.context).data('value');
  delete _videos_values[value];  
}

function submit_form_video(object, event){
  event.preventDefault();
  event.stopPropagation();

  if(!validate_url_video()){
    add_errors_video('La url no puede ser vacio', 'success_video_form_url');
    return false;
  }

  var form = $('#jr_form_video');
  var url  = $(form).attr('action');
  var type = $(form).attr('method');
  var data = $(form).serialize();

  init_load_video(object);
        
  // the ajax post
  $.ajax({
    url: url,
    type: type,
    data: data,
    success: function(response) {
      add_video(response);
      update_videos();
      close_modal_video();

      return;
    },
    error: function(response){
      add_errors_video(response.error, 'success_video_form_url');
    },
    complete: function(response){
      hide_load_video(object);
    }
  });

  return false;
}

function close_modal_video() {
  $.fancybox.close();
}

function hide_load_video(object){
  $(object).removeAttr('disabled');
  $(object).find('i').attr('class','icon-envelope');  
}

function init_load_video(object){
  $(object).attr('disabled', 'disabled');
  $(object).find('i').attr('class','icon-spinner icon-spin');
}

function add_errors_video(msg, id) {
  var message = $('<ul><li>' + msg +'</li></ul>');

  var target = $('#'+id);

  target.popover({
      content: message,
      trigger: 'hover',
      html: true,
      placement: 'right',
      template: '<div class="popover"><div class="arrow"></div><div class="popover-inner"><div class="popover-content"><p></p></div></div></div>'
  });

  $(target).addClass('field-error');

  event_hide_errors('jr_form_video');

  return false;
}

function validate_url_video(){
  var input = $('#success_video_form_url');

  if ($(input).val() != "") {
    return true;
  }

  return false;
}

function submit_form_video_profile(object, event){
  event.preventDefault();
  event.stopPropagation();

  if(!validate_url_video()){
    add_errors_video('La url no puede ser vacio', 'success_video_form_url');
    return false;
  }

  var form = $('#jr_form_video');
  var url  = $(form).attr('action');
  var type = $(form).attr('method');
  var data = $(form).serialize();

  init_load_video(object);
        
  // the ajax post
  $.ajax({
    url: url,
    type: type,
    data: data,
    success: function(response) {
      _videos_values[response.media] = response.media;
      $('#success_video_form_url').val('');
      close_modal_video();
      hide_load_video(object);

      var _template_video = 
      '<div class="cloud ui-widget-content draggable" data-id="__ID__">'+
      '<a href="//www.youtube.com/embed/__REFERENCE__?autoplay=1" class="fancybox fancybox.iframe">'+
      '<img src="__THUMB__" width="72" height="72" /></a>'+
        '<div class="info">'+
          '<h6 title="__NAME__">__NAME__</h6>'+
          '<p title="__AUTHOR__">__AUTHOR__</p>'+
        '</div>'+
        '<a href="javascript:void(0)">+</a>'+
      '</div>';
    
      var _template = _template_video.replace(/__ID__/g, response.media).replace(/__REFERENCE__/g, response.reference).replace(/__THUMB__/g, response.thumbnailUrl).replace(/__NAME__/g, response.name.substring(0, 35)).replace(/__AUTHOR__/g, response.author.substring(0, 25));

      $('#container-videos').prepend($(_template));
      $('.draggable').draggable({ revert: "valid" });

      //Si container-videos esta vacio
      $('#msg-video-none').remove();
      $('#container-videos').parent('.soundcloud').show();

      return;
    },
    error: function(response){
      add_errors_video(response.error, 'success_video_form_url');
    },
    complete: function(response){
      hide_load_video(object);
    }
  });

  return false;
}

//######################################################################
//############################ SOUNDCLOUDS #############################
//######################################################################
function validate_url_soundcloud() {
  var input = $('#success_soundcloud_url');

  if ($(input).val() != "") {
    return true;
  }

  return false;
}



function submit_form_soundcloud_profile(object, event){
  event.preventDefault();
  event.stopPropagation();

  if(!validate_url_soundcloud('success_soundcloud_url')){
    add_errors_video('La url no puede ser vacio', 'success_soundcloud_url');
    return false;
  }

  var form = $('#jr_form_soundcloud');
  var url  = $(form).attr('action');
  var type = $(form).attr('method');
  var data = $(form).serialize();

  init_load_video(object);
        
  // the ajax post
  $.ajax({
    url: url,
    type: type,
    data: data,
    success: function(response) {
      _videos_values[response.media] = response.media;
      $('#success_soundcloud_url').val('');
      close_modal_video();
      hide_load_video(object);

      var _template_video = 
      '<div class="cloud ui-widget-content draggable" data-id="__ID__">'+
      '<a href="__REFERENCE__" class="fancybox fancybox.iframe">'+
      '<img src="__THUMB__" width="72" height="72" /></a>'+
        '<div class="info">'+
          '<h6 title="__NAME__">__NAME__</h6>'+
          '<p title="__AUTHOR__">__AUTHOR__</p>'+
        '</div>'+
        '<a href="javascript:void(0)">+</a>'+
      '</div>';
    
      var _template = _template_video.replace(/__ID__/g, response.media).replace(/__REFERENCE__/g, response.reference).replace(/__THUMB__/g, response.thumbnailUrl).replace(/__NAME__/g, response.name.substring(0, 35)).replace(/__AUTHOR__/g, response.author.substring(0, 25));

      $('#container-music').prepend($(_template));
      $('.draggable').draggable({ revert: "valid" });

      //Si container-videos esta vacio
      $('#msg-sound-none').remove();
      $('#container-music').parent('.soundcloud').show();
      return;
    },
    error: function(response){
      add_errors_video(response.error, 'success_soundcloud_url');
    },
    complete: function(response){
      hide_load_video(object);
    }
  });

  return false;
}