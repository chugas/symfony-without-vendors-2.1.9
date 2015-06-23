/**
* Initialize rich textarea plugin
*
* Currently it accepts one parameter which is an array of trigger objects.
*
* Each trigger object consists of:
*		trigger	a single character trigger. (e.g. #, @, ! etc)
*		callback	a callback function that accepts on parameter, a trigger word and returns a set of matches.
*/
function setup_rich_textarea() {
  $( '#RICH_TEXTAREA' ).rich_textarea({
      /**
      * trigger definitions.
      *
      * rich_textarea supports defining multiple trigger words each with their own
      * autocomplete data source callback
      *
      * It's an array of objects. Each object has keys:
      *
      *	trigger - the trigger character used to invoke the autocomplete
      *	callback - the callback to invoke once a trigger has been identified.
      */
      triggers: [{
          /**
          * generates autocomplete list for @mention
          *
          * For demonstration purposes only. In a real application this method
          * would likely do some ajax call to a server to get the autocomplete list
          * based on the term provided. 
          *
          * @param {String} term @term entered 
          * @param {Function} response jquery ui autocomplete response callback.
          *
          * @see http://api.jqueryui.com/autocomplete/#option-source
          */
          trigger: '@',
          callback: function( term, response ){
            $.ajax({
              url: '/suggest/',
              cache: true,
              data: { 'term': term, 'type': 'user' },
              success: function(data){
                var tags = new Array();
                var elem;
                for (var i in data ) {
                  elem = { label: data[i].name, value: { value: '[user]'+data[i].id+'[/user]', content: '<span class="nombre"><a href="/usuario/'+data[i].id+'">'+data[i].name+'</a></span>' } };
                  tags.push(elem);
                }
                response( $.ui.autocomplete.filter( tags, term ) );
              }
            });
          }}
      ],
      /**
       * regex definition
       *
       * regexes can be defined to invoke callbacks when the user enters certain patterns.
       *
       * callbacks are passed an object with startNode, startOffset, endNode, endOffset and word keys
      */
      regexes: [
          {
          regex: /(?:http|https):\/\/?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(.+)/g,
          callback: function( word_entry ){
            var str = word_entry.word;
            var iframe = str.replace(/(?:http|https):\/\/?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(.+)/g, '<iframe width="420" height="345" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>');
            //console.log( "regex YOUTUBE: got word entry:" + iframe );
            $( '#RICH_TEXTAREA' ).rich_textarea( 'replaceWord', word_entry, iframe, '[video]' + word_entry.word + '[/video]' );
          }},
          {
          regex: /(http|https):\/\/([^\"]*).(png|gif|jpg|jpeg)/g,
          callback: function( word_entry ){
            $( '#RICH_TEXTAREA' ).rich_textarea( 'replaceWord', word_entry, '<img width="420" src="' + word_entry.word + '">' + word_entry.word + '</img>', '[img]' + word_entry.word + '[/img]' );
          }},
          {
          regex: /^(((http|https):\/\/)?([\-\w]+\.)+\w{2,5}(\/[%\-\w]+(\.\w{2,})?)*(([\w\-\.\?\\/+@&#;`~=%!]*)(\.\w{2,})?)*\/?)$/i,
          callback: function( word_entry ){
            if( !word_entry.word.match(/(?:http|https):\/\/?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(.+)/g) && !word_entry.word.match(/(http|https):\/\/([^\"]*).(png|gif|jpg|jpeg)/g) ) {
              //console.log( "regex: got word entry:", word_entry );
              var href = ( word_entry.word.match('http')===null ? 'http://'+word_entry.word : word_entry.word);
              $( '#RICH_TEXTAREA' ).rich_textarea( 'replaceWord', word_entry, '<a href="' + href + '">' + word_entry.word + '</a>', '[url]' + href + '[/url]' );
            }
          }}
      ]
  });
}
