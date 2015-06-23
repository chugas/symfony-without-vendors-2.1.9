function more(self){
  var collectionContainer = $('#' + $(self).data('container'));
  var _HREF = $(self).data('href');

  var div_pager = $(self).parents('.success-pager-container');

  $(div_pager).find('.link-pager').hide();
  $(div_pager).find('.link-load').show();
  
  $.ajax({
    url: _HREF,
    cache: true,
    success: function(json){
      collectionContainer.append($(json.response));
      // llamar a una funcion
      if (typeof post_page_success == 'function'){
        post_page_success();
      }      
      if(json.haveToPaginate == '1'){
        $(div_pager).find('.link-pager').show();
        if(json.hasOwnProperty('href')){
          $(self).data('href', json.href);
        }
      }else{
        $(div_pager).find('.link-pager').hide();
      }
      $(div_pager).find('.link-load').hide();
    }
  });
  return false;
}

$(document).ready(function() {
  $('a[data-pager-button="add"]').on('click', function (e) {
    e.preventDefault(); 
    return more(this);
  });
});
