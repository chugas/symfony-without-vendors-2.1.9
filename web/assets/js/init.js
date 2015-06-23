/*window.onload = function()
{
  var lis = document.getElementById('cssdropdown').getElementsByTagName('li');
  for(i = 0; i < lis.length; i++)
  {
    var li = lis[i];
    if (li.className == 'headlink')
    {
      li.onmouseover = function() {
        this.getElementsByTagName('ul').item(0).style.display = 'block';
      }
      li.onmouseout = function() {
        this.getElementsByTagName('ul').item(0).style.display = 'none';
      }
    }
  }
}*/

$(document).ready(function(){	
  $("#slider").easySlider({
    auto: true, 
    continuous: true
  });
  
  $('.fancybox').fancybox({
    fitToView : false,
    autoSize : true,
    closeClick : false,
    closeBtn : false,
    margin: 0,
    padding: 0,
    openEffect : 'fade',
    closeEffect : 'none'
  });
});	

/*$(function() {
  $( "#accordion" ).accordion();
});*/