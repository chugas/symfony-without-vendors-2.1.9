if ( typeof( ddt ) == 'undefined' )
{
  var ddt = 
  {

    on: function() {},
    off: function() {},
    log: function() {},
    warn:	function() {},
    error: function() {}

  };

}

(function($) {

  var RANGE_HANDLER = document;
  var CREATERANGE_HANDLER = document;


  $.widget( 'ui.rich_textarea', 
  {

    options: 
    {

      triggers: [],


      regexes: []

    },


    _create: function() 
    {


      var richtext_widget = this;

      this.current_trigger = false;

      this.currentRange = false;

      this.selectionEntered = false;


      this.do_regex = true;

      this.autocomplete_open = false;

      // this.element.focus();

      // widget factory style event binding. First is the name of the 
      // event mapped onto the string name of the method to call.

      this._on( 
      {
        keyup: function( event ) {
          this._onKeyUp( event );
        },
        keypress: function( event ) {
          this._onKeyPress( event );
        },
        keydown: function( event ) {
          this._onKeyDown( event );
        },
        mouseup: function( event ) {
          this._onMouseUp( event );
        },
        focus: function( event ) {
          this._onFocus( event );
        },
        paste: function( event ) {
          this._onPaste( event );
        },
        prepaste: function( event ) {
          this._onPrePaste( event );
        },
        postpaste: function( event ) {
          this._onPostPaste( event );
        }
      });

      this.element.autocomplete(
      {

        html: 'html',

        blur: function( event, ui )
        {
          ddt.log( "autocompete blur event" );
        },

        open: function( event, ui )
        {
          ddt.log( "autocomplete open: event" );
          richtext_widget.autocomplete_open = true;
        },


        close: function( event, ui )
        {
          ddt.log( "autocomplete close: event" );
          richtext_widget.autocomplete_open = false;
          richtext_widget.do_regex = true;
        },


        change: function( event, ui )
        {
          ddt.log( "autocomplete change event" );
        },

        source: function( request, response )
        {

          ddt.log( "source(): got source event" );

          // this might be NULL when set in a mouseup event.

          var trigger_entry = richtext_widget._checkForTrigger();

          if (( trigger_entry == false ) || ( trigger_entry == null ))
          {

            ddt.log( "source(): no trigger" );

            return;
          }

          ddt.log( "source(): got source event with trigger ", trigger_entry );

          // we're passing in the trigger term outside the normal autocomplete
          // path, so the minLength option in autocomplete doesn't work. We'll set it 
          // as 2 here for now.

          if (( trigger_entry != false ) &&
            ( trigger_entry.word.length >= 2 ))
            {

            ddt.log( "source(): invoking response" );

            // this causes the autocomplete menu to be populated

            trigger_entry.callback( trigger_entry.word, response );

          }
					
        },

        focus: function( event, ui )
        {

          ddt.log( "focus(): autocomplete got focus event", ui );

          event.preventDefault();

          return false;
        },

        select: function( event, ui )
        {

          ddt.log( "select(): got select event ", event, " and ui element ", ui, " current range is ", this.currentRange );

          // replace the trigger word, including trigger character, with the selected
          // value.

          richtext_widget._insertSelection( richtext_widget.current_trigger, ui.item.value );

          // it's just been inserted so the cursor should be right behind the thing. 

          richtext_widget._highlightObject();

          // prevent jQuery autocomplete from replacing all content in the div.

          event.preventDefault();

          // prevent regexes from running.

          richtext_widget.do_regex = false;

        }

      });	// end of this.element.autocomplete
				
    },	
    _onKeyDown: function( event )
    {

      ddt.log( "_onKeyDown(): with key '" + event.keyCode + "' event: ", event );

      var object = null;
      var location = null;
      var sel = null;

      // if the autocomplete menu is open don't do anything with up and down arrow keys

      if ( this.autocomplete_open )
      {

        if (( event.which == $.ui.keyCode.UP ) || ( event.which == $.ui.keyCode.DOWN ) || ( event.which == $.ui.keyCode.ENTER ))
        {
          event.preventDefault();
          return true;
        }
      }

      // we may have a multiple char selection in which case we want to let the browser handle
      // it. (see _onKeyUp)

      sel = RANGE_HANDLER.getSelection();

      if ( sel.rangeCount )
      {

        var range = RANGE_HANDLER.getSelection().getRangeAt(0);

        if ( range.collapsed == false )
        {
          ddt.log( "_onKeyDown(): multi-char range. skipping onKeyDown processing" );
          return;
        }
      }


      switch ( event.which )
      {

        case 0:

          // is i tbetter to have it do nothing than to make the contents of the editable div
          // inconsistent? In my testing with Chrome 17.0.963.56 under Linux, we only get a 0 with 
          // the number pad delete key. Seems to work correctly on other platforms.

          ddt.error( "_onKeyDown(): WEBKIT BUG: Keycode 0 returned. probably delete on Linux keypad but can't be sure" );

          // event.preventDefault();

          break;

        case $.ui.keyCode.BACKSPACE:

          ddt.log( "_onKeyDown(): BACKSPACE pressed" );

          var retval = false;

          if (( retval = this._backspaceZeroSpace()) == 'stop' )
          {
            ddt.log( "_onKeyDown(): BACKSPACE: stop word encountered. stopping" );
            return;
          }

          // is the previous character an embedded object?

          ddt.log( "_onKeyDown(): BACKSPACE: checking to see if we are next to an object" );

          if ( object = this._checkForAdjacentObject( 'left' ))
          {

            ddt.log( "_onKeyDown(): backspacing over an object :", object );

            this.deleteObject( object.dom_node );

            // if we deleted any whitespace characters prevent the browser from
            // deleting whatever the next character is

            if ( object.preventDefault )
            {
              event.preventDefault();
            }

          }

          ddt.log( "_onKeyDown(): BACKSPACE: done" );

          break;

        case $.ui.keyCode.DELETE:

          ddt.log( "_onKeyDown(): DELETE pressed" );

          var retval = false;

          if (( retval = this._deleteZeroSpace()) == 'stop' )
          {
            ddt.log( "_onKeyDown(): DELETEE: stop word encountered. stopping" );
            return;
          }

          // is the next character an embedded object?

          ddt.log( "_onKeyDown(): DELETE: checking to see if we are next to an object" );

          if ( object = this._checkForAdjacentObject( 'right' ))
          {

            ddt.log( "_onKeyDown(): DELETE: deleting an object :", object );

            this.deleteObject( object.dom_node );

            // after the delete we may be edge up against another object. 

            event.preventDefault();

          }

          ddt.log( "_onKeyDown(): DELETE done" );

          break;

        case $.ui.keyCode.LEFT:

          ddt.log( "_onKeyDown(): LEFT pressed" );

          // where is the caret now? 

          var caret = this._getCaretPosition();

          ddt.log( "_onKeyDown(): _getCaretPosition() returned node '" + caret.dom_node + "' with offset '" + caret.offset + "'" );

          // are we immediately next to an object? Jump the cursor over it.

          if ( object = this._isEmbeddedObject( caret.dom_node ) )
          {

            ddt.log( "_onKeyDown(): setting caret before object: ", object );

            // this._setCaretPositionRelative( object, 'before' );

            this._setCaretPositionRelative( object.previousSibling, 'end' )

            // FIXME: prevent the caret from jumping an additional space.
            //
            // In WebKit in the left arrow case without preventDefault() the 
            // caret jumps an additional position. HOWEVER, in the right case
            // with it enabled the caret does not jump over the end of the span
            // even if I select it.

            event.preventDefault();

            break;
          }

          // We are not right next to an object but there may be some number of zero width
          // characters and/or textnodes between the current caret position and the object
          //
          // _moveCaret() may move the cursor into another container in which
          // case we do NOT try to jump over any next object.

          if ( location = this._moveCaret( caret.dom_node, caret.offset, 'left' ) )
          {

            ddt.log( "_onKeyDown(): LEFT: _moveCaret returned:", location );

            // unless we've just moved into a container (i.e. new line) , we want to jump over any
            // embedded objects we've arrived next to.

            if (( location.checkForObjects ) && ( object = this._checkForAdjacentObject( 'left' )))
            {

              ddt.log( "_onKeyDown(): LEFT: jumping over object to the left" );

              this._setCaretPositionRelative( object.dom_node.previousSibling, 'end' )

              event.preventDefault();

            }

            // HACK: special fix for WebKit. When jumping out of containers don't let the 
            // browser do it's thing otherwise it'll jump one too far. Same for Mozilla.

            if ( location.preventDefault )
            {
              ddt.log( "_onKeyDown(): Leaving a container, etc. preventing default" );

              event.preventDefault();
            }

          // If it's a text node we want to let the browser move the cursor for us. 

          }

          ddt.log( "_onKeyDown(): LEFT done" );

          break;

        case $.ui.keyCode.RIGHT:

          ddt.log( "_onKeyDown(): RIGHT pressed" );

          // where is the caret now? 

          var caret = this._getCaretPosition();

          ddt.log( "_onKeyDown(): _getCaretPosition() returned node '" + caret.dom_node + "' with offset '" + caret.offset + "'" );

          // are we next to an object? Jump the cursor over it.

          if ( object = this._isEmbeddedObject( caret.dom_node ) )
          {

            ddt.log( "_onKeyDown(): isEmbedded true. moving to beginning of nextSibling" );

            this._setCaretPositionRelative( object.nextSibling, 'beginning' )

            // FIXME: prevent the caret from jumping an additional space.
            // This does not make sense to me. In the LEFT arrow case without the preventDefault
            // the cursor jumps an additional space in WebKit. However, in the RIGHT arrow case
            // if I preventDefault, the caret cannot be moved outside of the span when jumping over
            // an object. Clearly there is something I do not understand.

            // event.preventDefault();

            break;

          }

          // _moveCaret() may move the cursor into another container in which
          // case we do NOT try to jump over any next object.

          if ( location = this._moveCaret( caret.dom_node, caret.offset, 'right' ) )
          {

            ddt.log( "_onKeyDown(): RIGHT: after skipping over zero space. checking for adjacent objects :", location );

            // if we are right next to an object we want to jump the
            // cursor over it but ONLY if we are not moving onto a new line (i.e. moving into
            // a child container)

            if (( location.checkForObjects ) && ( object = this._checkForAdjacentObject( 'right' )))
            {

              ddt.log( "_onKeyDown(): RIGHT: right arrowing over an object" );

              this._setCaretPositionRelative( object.dom_node.nextSibling, 'beginning' )

            // FIXME: see above. preventingDefault() here breaks jumping across objects.

            // event.preventDefault();
		
            }

            // HACK: special fix for WebKit. When jumping into or out of containers don't let the 
            // browser do it's thing otherwise it'll jump one too far. Same for Mozilla and a BR.

            if ( location.preventDefault )
            {
              ddt.log( "_onKeyDown(): entering or leaving a container. preventing default" );

              event.preventDefault();
            }

          }

          ddt.log( "_onKeyDown(): RIGHT done." );

          break;

        case $.ui.keyCode.ENTER:

          // the trick that took so long to figure out is that we can actually prevent the default
          // behavior of the browser by preventing the default behavior in the onKeyDown event.
          //
          // Without this, each major browser puts different markup in the div. 
          //
          // @see _handleEnter()

          this._handleEnter( event );

          // prevent default behavior

          return false;

          break;

      }	// end of switch

    },	
    _onKeyPress: function( event )
    {

      ddt.log( "_onKeyPress(): with key '" + event.keyCode + "' event: ", event );

      // if the autocomplete menu is open don't do anything with up and down arrow keys

      if ( this.autocomplete_open )
      {

        if (( event.which == $.ui.keyCode.UP ) || ( event.which == $.ui.keyCode.DOWN ) || ( event.which == $.ui.keyCode.ENTER ))
        {
          event.preventDefault();
          return true;
        }
      }

      switch ( event.which )
      {

        case $.ui.keyCode.SPACE:

          // on pressing SPACE check to see if we match any regexes.

          ddt.log( "_onKeyDown(): SPACE pressed. Checking regexes" );

          this._checkRegexes( event );

          break;

        case $.ui.keyCode.ENTER:

          // we have to be careful not to let regexes conflict with the autocomplete
          // dropdown. The dropdown catches the keypress event for ENTER but does not, for 
          // whatever reason, stop propagation so we get that keypress here. 

          ddt.log( "_onKeyDown(): ENTER pressed. Checking regexes" );

          this._checkRegexes( event );

          break;

      }

    },	

    _onKeyUp: function( event )
    {

      var caret = null;

      ddt.log( "_onKeyUp(): with key '" + event.which + "':", event );

      // if the autocomplete menu is open don't do anything with up and down arrow keys

      if ( this.autocomplete_open )
      {

        if (( event.which == $.ui.keyCode.UP ) || ( event.which == $.ui.keyCode.DOWN ) || ( event.which == $.ui.keyCode.ENTER ))
        {
          event.preventDefault();
          return true;
        }
      }

      // we may have a multiple char selection

      if ( this._handleRangeSelection() )
      {

        // let the user do with the range whatever they want. The browser will 
        // take care of it. 

        return;

      }

      // save the range in case we click out of the div and then want to insert
      // something.

      this._saveRange();

      // if we are at the end of an object, highlight it to indicate 
      // that it'll get deleted on backspace.

      this._highlightObject();

      // we may arrive here because of arrow key and other events.

      switch ( event.which )
      {

        case $.ui.keyCode.ENTER:
					
          ddt.log( "_onKeyUp(): ENTER: pressed. Closing autocomplete menu." );

          // we close the autocomplete menu on any of these keys.

          this.element.autocomplete( 'close' );
          this.current_trigger = false;

          // FIXME: see  _insertSelection(). jQuery (or maybe my) bug where this
          // callback is still getting invoked even when enter is pressed in the autocomplete
          // dropdown.

          if ( this.selectionEntered )
          {
            this.selectionEntered = false;
            break;
          }

          this._onEnterFixUp( event );

          ddt.log( "_onKeyUp(): ENTER. done." );

          break;

        case $.ui.keyCode.SPACE:
        case $.ui.keyCode.TAB:
        case $.ui.keyCode.HOME:
        case $.ui.keyCode.END:

          // we close the autocomplete menu on any of these keys.

          this.element.autocomplete( 'close' );
          this.current_trigger = false;

          ddt.log( "_onKeyUp(): closed autocomplete menu" );

          break;

        case $.ui.keyCode.LEFT:
        case $.ui.keyCode.RIGHT:
        case $.ui.keyCode.BACKSPACE:

          ddt.log( '_onKeyUp(): arrow/backspace pressed' );

          // using CNTRL-LEFT AND RIGHT it's possible to get inside an object. 

          caret = this._getCaretPosition();

          var object_node = this._clickedOnObject( caret.dom_node );

          if ( object_node != false )
          {
						
            ddt.log( "_onKeyUp(): currently in an object. moving caret before object" );

            this._setCaretPositionRelative( object_node, 'before' );
            return;
          }

          // have we moved beyond the limit of a trigger character? 

          if ( ! this._checkForTrigger() ) 
          {

            ddt.log( "_onKeyUp(): outside of trigger" );

            this.element.autocomplete( 'close' );
            this.current_trigger = false;

          }
          else
          {

            ddt.log( '_onKeyUp(): in trigger' );

            // autocomplete BUG? If we do not pass a value for the second argument here
            // search is not invoked.
            //
            // We call this here to force the autocomplete menu open if we move the cursor
            // over a trigger word. autocomplete does not do that automatically.

            this.element.autocomplete( 'search', 'test' );

          }

          break;

        case $.ui.keyCode.UP:
        case $.ui.keyCode.DOWN:

          // it's always something. WebKit, if you delete the newline at the beginning
          // of the line (wrapping div) will delete any embedded contenteditable=false spans
          // when joining the lines. Ugh.
          //
          // So this means everything needs to be contenteditable so we need to enforce NOT
          // moving into the middle of an embedded object ourselves. 
          //
          // So if the user moves up or down into an object move the cursor to the beginning of
          // the object.

          caret = this._getCaretPosition();

          var object_node = this._clickedOnObject( caret.dom_node );

          if ( object_node != false )
          {
            this._setCaretPositionRelative( object_node, 'before' );
          }

          break;

      }	// end of switch over keys.

    },	
    _onMouseUp: function( event )
    {

      var trigger_entry;

      // make sure the autocomplete menu is closed.

      this.element.autocomplete( 'close' );

      // scrollbar causes this event to fire so we need to guard against the fact
      // the editable div may not have focus.

      if ( ! $( '#' +  this.element.attr( 'id' ) ).is( ":focus" ) )
      {
        ddt.log( "_onMouseUp(): the div does not have focus" );
        return true;
      }

      if ( this._handleRangeSelection() )
      {

        // let the user do with the range whatever they want. The browser will 
        // take care of it. 

        return;
      }

      // save the range in case we click out of the div and then want to insert
      // something.

      this._saveRange();

      ddt.log( "_onMouseUp(): did we click on an object?:", event.target );

      var object_node = this._clickedOnObject( event.target );
				
      if ( object_node != false )
      {

        ddt.log( "_onMouseUp(): preventing default action" );

        this._setCaretPositionRelative( object_node, 'before' );

        event.preventDefault();
      }

      // if we are at the end of an object, highlight it to indicate 
      // that it'll get deleted on backspace.

      this._highlightObject();

      if ( trigger_entry = this._checkForTrigger() )
      {
				
        ddt.log( "_onMouseUp(): calling autocomplete from onMouseUp handler with term '" + trigger_entry.word + "'" );

        // FIXME: for some reason when _checkForTrigger() is called from the autocomplete source callback
        // the selection is lost. So _checkForTrigger() sets a class level copy which source checks. Ugly.

        this.element.autocomplete( 'search', 'test' );

      }

    },	

    _onFocus: function( event )
    {

      ddt.log( "focus(): top" );

      if ( this.autocomplete_open )
      {

        ddt.log( "_onFocus(): autocomplete menu is open" );
        event.preventDefault();
        return false;
      }

    },

    _onPaste: function( event )
    {

      ddt.log( "_onPaste(): got paste event ", event );

      //$( this ).trigger("prepaste");

      this._onPrePaste( event );

      var self = this;

      // the callback timeout here is not perfect. If the browser is
      // especially slow this may fail.

      setTimeout( function() 
      { 
        self._onPostPaste( event ); 
      }, 75);

      // this._checkRegexes( event );

      return true;

    // return this.replaceWith(this.html().replace(/<\/?[^>]+>/gi, ''));

    },

    _onPrePaste: function( event )
    {

      ddt.log( "_onPrePaste(): top" );

      $(this.element).find("*").each(function()
      {
        //var tmp=new Date.getTime();
        $(this).data("uid", '123');
      });
    },

    
    _onPostPaste: function( event )
    {

      ddt.log( "_onPostPaste() top" );

      var rich_textarea = this;

      // replace any html tags but ignore text nodes

      this.element.find("*").each(function()
      {
        if(!$(this).data("uid"))
        {

          ddt.log( "Found a new element '" + $(this).get(0).tagName + "' of type", $(this).get(0).nodeType );

          // KLUDGE: links copied from a browser bar are wrapped in <a> tags but if we strip the
          // a tags the selection is lost. So for the moment we'll leave the a tag in place.

          if (( $(this).get(0).nodeType != 3 ) && ( $(this).get(0).nodeName != 'A' ))
          {

            // This unfortunately messes up the selection.

            $(this).replaceWith( $(this).text() );
          }

        // $(this).removeClass();
        // $(this).removeAttr("style id");
        }
      });

      this._checkRegexes( event );

    },

    _onEnterFixUp: function( event )
    {

      var caret = this._getCaretPosition();

      ddt.log( "_onEnterFixUp(): current DOM Node is:", caret.dom_node );

      // ---------------------------------------
      // Fixup any previous sibling or container
      // ---------------------------------------

      // in WebKit the previousSibling should always be null since we should be at the beginning
      // of a DIV containing the new line. For this case, we examine the previous sibling of
      // our containing DIV. Chrome is fond of inserting empty <DIV><BR></DIV>'s.
      //
      // FireFox/Mozilla on the other hand inserts <BR _moz_dirty=""> which means there should always
      // be a previousSibling. 

      if ( caret.dom_node.previousSibling == null )
      {

        ddt.log( "_onEnterFixUp(): previous sibling is NULL. Likely a WebKit browser." );

        this._checkSibling( caret.dom_node.parentNode, 'prev' );

      // is our parents previousSibling a container? 

      }
      else if ( caret.dom_node.previousSibling.nodeType != 3 )
      {

        ddt.log( "_onEnterFixUp(): Previous sibling is NOT a text node" );

        // not matter what it is, make sure it's wrapped in empty text nodes. 

        this._insertEmptyNode( caret.dom_node.previousSibling, 'before' );
        this._insertEmptyNode( caret.dom_node.previousSibling, 'after' );

      }

      // ---------------------------------------
      // Fixup nodes moved to the new line
      // ---------------------------------------

      // if we are Mozilla, instead of a <DIV> it adds <BR _moz_dirty="">
      // WebKit is known for adding <DIV><BR></DIV> for a new line.
      //
      // in either case, we'll make sure there are empty text nodes around it for good measure.

      if ( $( caret.dom_node ).filter( '[_moz_dirty]' ).length != 0 )
      {

        ddt.log( "_onEnterFixUp(): mozilla BR. wrapping in textnodes" );

        this._insertEmptyNode( caret.dom_node, 'before' );
        this._insertEmptyNode( caret.dom_node, 'after' );

      }
      else if ( caret.dom_node.nodeName == 'BR' )
      {

        // this is likely webKit. We'll attempt to replace the BR with a space. 

        ddt.log( "_onEnterFixUp(): webkit BR." );

      // this seems to muck up webkit. ENTER keys starts inserting nested DIVs. Not
      // sure why.

      // this._insertEmptyNode( caret.dom_node, 'before' );
      // $( caret.dom_node ).remove();

      }
      else if ( this._isEmbeddedObject( caret.dom_node ) )
      {

        // if we are an object, make sure there's a node in front of us and select it so the cursor
        // doesn't try to get in the span of the object.

        var textnode = this._insertEmptyNode( caret.dom_node, 'before' );
        this._selectTextNode( textnode, 0 );

      }
      else if (( caret.dom_node.nodeName == 'DIV' ) || 
        ( caret.dom_node.nodeName == 'SPAN' ) ||
        ( caret.dom_node.nodeName == 'P' ))
        {

        // This branch is not likely to happen as most container should have textnodes
        // in front of them, but just in case is this a container? (could maybe be an embedded 
        // object that we have just pressed ENTER immediately before.)
        //
        // else we are webkit or MSIE. webkit adds a DIV, MSIE a P. 
        // make sure the first child is a text node.

        if ( caret.dom_node.childNodes.length == 0 )
        {

          // empty container. 

          ddt.log( "_onEnterFixUp(): adding zero width space to empty container (div/p)" );

          this._insertEmptyNode( caret.dom_node, 'child' );

        }
        else if ( caret.dom_node.childNodes[ 0 ].nodeType != 3 )
        {

          // first node of the container is NOT a textnode.

          ddt.log( "_onEnterFixUp(): first child of container is a '" + caret.dom_node.childNodes[ 0 ].nodeName + "'" );

          // make sure it's wrapped in textnodes

          this._insertEmptyNode( caret.dom_node.childNodes[ 0 ], 'before' );
          this._insertEmptyNode( caret.dom_node.childNodes[ 0 ], 'after' );

        }

      }	// end of the node was a container

      //                        $( '.scrollto' ).scrollintoview( { duration: 30 });
      //                        $( '.scrollto' ).get(0).scrollIntoView(false);

      // if there is a scrollTo span set in handleEnter() invoke scrollTo.

      if ( $( '.scrollto' ).length  )
      {
        ddt.log( "calling scrollTo" );
        $( '#' +  this.element.attr( 'id' ) ).scrollTo( $( '.scrollto' ), 20 );
        $( '.scrollto' ).remove();
      }

    // $( this.tmp_kludge ).scrollintoview();

    },	
    _handleEnter: function( event )
    {
      ddt.log( "top of handleEnter()" );

      event.preventDefault();

      // we insert a <BR> where the cursor currently is. It may, however, be inside a text node
      // which means the text node needs to be split.

      var sel = RANGE_HANDLER.getSelection();
      var range = this.currentRange;

      ddt.log( "got range and selection", range );

      sel.removeAllRanges();

      if ( ! range )
      {

        // chances are someone just clicked ENTER.

        var br = $( '<br>' );

        ddt.log( "_handleEnter(): adding br to id = '" + this.element.attr( 'id' ) + "'" );

        var node = $( '#' + this.element.attr( 'id' ) );

        $( br ).appendTo( node  );

        var textnode = this._insertEmptyNode( br.get(0), 'before', true );
        var textnode = this._insertEmptyNode( br.get(0), 'after', true );

        // patch in a selection

        range = rangy.createRange();
        sel = rangy.getSelection();

        range.setStart( textnode, 0 );
        range.setEnd( textnode, 0 );
        range.collapse( false );

        sel.removeAllRanges();
        sel.addRange(range);
        sel.refresh();

        ddt.log( "_handleEnter(): range is ", range );

        this.focus();

        // this._saveRange();

        return false;
      }

      sel.addRange( range );

      var node = $( '<br>' );
	
      var dom_node = node.get(0);

      range.insertNode( node.get(0) );
      range.setStartAfter( node.get(0) );
      range.setEndAfter( node.get(0) );
      range.collapse( false );

      sel.removeAllRanges();
      sel.addRange(range);

      ddt.log( "handleEnter(): previousSibling is : ", dom_node.previousSibling );

      // check siblings before and after us, if any.
      //
      // And, in Chome and possibly other browsers, if this is the first element there is,
      // an entirely empty text node is insert at the first position.

      ddt.log( "handleEnter(): inserting zero width node before selection" );

      // FIXME: Not sure why, but if I don't force the inclusion of empty nodes even if
      // the object is surrounded by text nodes selections break. wtf? (i.e. without this
      // inserting object into the middle of text lines fails in Webkit)

      var textnode = this._insertEmptyNode( dom_node, 'before', true );

      // if there is no sibling after us or if it's not a text node, add a zero width space.

      ddt.log( "handleEnter(): inserting zero width node after selection" );

      var textnode2 = this._insertEmptyNode( dom_node, 'after', true );

      // FIXME: if this is 0, in Chrome it selects a point in the span.

      this._selectTextNode( textnode2, 1 );

      // scrollintoview doesn't seem to work with a <BR> so insert an empty span,
      // scroll to it and then delete the span. Ugly hack.

      var tmp_span = $( '<span class="scrollto"></span>' );
			
      tmp_span.insertAfter( node );

      return false;

    },	
    _handleRangeSelection: function()
    {

      var sel = RANGE_HANDLER.getSelection();

      // if there's no selection, which can happen if we are scrolling, this will throw
      // an exception

      try 
      {
        var range = sel.getRangeAt(0);
      }
      catch( err )
      {
        ddt.log( "getRangeAt() failed - no range?" );

        // no selected range.

        return false;
      }

      var start_node = null;
      var end_node = null;

      ddt.log( "_handleRangeSelection(): checking range for mult-char selection: ", range );

      // did the user click and drag a selection. We /should/ get the final selection
      // here unlike the case with the keyboard lagging. 

      if ( range.collapsed == false )
      {

        ddt.log( "_handleRangeSelection(): we have a multi-character selection" );

        if (( start_node = this._clickedOnObject( range.startContainer )) != false )
        {

          ddt.log( "_handleRangeSelection(): range starts in an object." );

          range.setStartBefore( start_node );

        }

        if (( end_node = this._clickedOnObject( range.endContainer )) != false )
        {

          ddt.log( "_handleRangeSelection(): range ends in an object." );

          range.setEndAfter( end_node );

        }

        if (( start_node != false ) || ( end_node != false ))
        {

          ddt.log( "_handleRangeSelection(): modifying range :", range );

          sel.removeAllRanges();
          sel.addRange( range );
        }

        return true;

      }	// end of if the user selected a multi-char range.

      return false;

    },	
    _saveRange: function( range )
    {
      ddt.log( "_saveRange(): before save currentRange: ", this.currentRange );

      // we may have been invoked because of a scrollbar move.

      if ( typeof( range ) == 'undefined' )
      {

        try
        {

          ddt.log( "_saveRange(): current selection is", RANGE_HANDLER.getSelection() );

          var range = RANGE_HANDLER.getSelection().getRangeAt(0);
        }
        catch( err )
        {

          ddt.log( "_saveRange(): no range? caught exception:" + err );
          return false;
        }

      }

      this.currentRange = range.cloneRange();

      ddt.log( "_saveRange(): saving currentRange: ", this.currentRange );
    },


    _checkForTrigger: function()
    {

      var caret = null;
      var trigger_entry = null;

      caret = this._getCaretPosition();

      if (( ! caret ) || ( caret.offset == -1 ))
      {

        ddt.log( "_checkForTrigger(): we are not inside a text node. No trigger" );

        return false;
      }

      ddt.log( "_checkForTrigger(): current caret position is " + caret.offset );

      // are we inside the bounds of a trigger word that may be interspersed zero width space
      // character and may span multiple text nodes? (thanks WebKit)
      //
      // -1 because the caret position is to the right of the last character entered.

      trigger_entry = this._isTrigger( caret.dom_node, caret.offset - 1 );

      this.current_trigger = trigger_entry;

      return trigger_entry;

    },	

    _checkRegexes: function( event )
    {

      var caret = null;
      var word_entry = null;

      ddt.log( "_checkRegexes(): with event: ", event );

      caret = this._getCaretPosition();

      if ( caret.offset == -1 )
      {

        ddt.log( "_checkRegexes(): we are not inside a text node. No word" );

        return false;
      }

      ddt.log( "_checkRegexes(): current caret position is " + caret.offset + " value is '" + caret.dom_node.nodeValue.charAt( caret.offset - 1 ) + "'" );

      if ( event.type =='keyup' )
      {

        // if the user pressed a space, then we need to start looking two characters back.

        if ( caret.offset < 3 )
        {
          return;
        }

        // we're in keyup so the cursor has moved past the space

        caret.offset--;

      }

      // are we inside the bounds of a word that may be interspersed zero width space
      // character and may span multiple text nodes? (thanks WebKit)
      //
      // -1 because the caret position is to the right of the last character entered.

      if ( word_entry = this._getWord( caret.dom_node, caret.offset - 1 ) )
      {

        ddt.log( "_checkRegexes(): found word '" + word_entry.word + "'" );

        // loop through the regex definitions checking each regular expression. If
        // we find a match, run the callback. We only run one match, the first match having
        // precedence.

        for ( var i = 0; i < this.options.regexes.length; i++ )
        {

          ddt.log( "_checkRegexes(): checking against '" + this.options.regexes[i].regex );

          if ( word_entry.word.match( this.options.regexes[i].regex ) )
          {

            ddt.log( "_checkRegexes(): found match at offset '" + i + "'" );

            this.options.regexes[i].callback( word_entry );

          }

        }

      }	// end of if we got a word.

    },	
    _isTrigger: function( dom_node, caret_position )
    {

      // modes are 'looking_for_trigger' and 'looking_for_space'

      var mode = 'looking_for_trigger';
      var trigger_entry = null;
      var loop_brake = 200;

      // used to remember where the trigger start character is.

      var trigger_start = {};

      // remember that we can inconveniently have zerospace characters anywhere after
      // inserts of lines and objects and subsequent deletes.
      //
      // search backwards for a trigger character.

      while ( true )
      {

        loop_brake--;

        if ( loop_brake <= 0 )
        {

          ddt.error( "_isTrigger(): runaway loop. braking" );

          return false;
        }

        if ( caret_position == -1 )
        {

          ddt.log( "_isTrigger(): top of loop, caret_position is '" + caret_position + "' previousSibling is:", dom_node.previousSibling );
		
          if ( dom_node.previousSibling == null )
          {
					
            ddt.log( "_isTrigger(): beginning of container found." );

            if ( mode == 'looking_for_trigger' )
            {

              ddt.log( "_isTrigger(): not a trigger" );

              return false;
            }

            break;	// out of while loop 
					
          }

          if ( dom_node.previousSibling.nodeType != 3 )
          {

            ddt.log( "_isTrigger(): previousSibling is NOT a text node." );

            if ( mode == 'looking_for_trigger' )
            {

              ddt.log( "_isTrigger(): not a trigger" );

              return false;
            }

            break;	// out of while loop 

          }

          dom_node = dom_node.previousSibling;

          caret_position = dom_node.nodeValue.length - 1;

          ddt.log( "_isTrigger(): moving to previousSibling length '" + caret_position + "'" );

          if ( caret_position == -1 )
          {

            // empty text nodes seem to be inserted by WebKit randomly.

            ddt.log( "_isTrigger(): zero length textnode encountered" );

            continue;

          }

        }	// end of if we are at the beginning of a textnode.

        // do we have a zero width space character? 

        if ( dom_node.nodeValue.charAt( caret_position ) == '\u200B' )
        {

          ddt.log( "_isTrigger(): skipping zero width space character" );

          caret_position--;

          continue;
						
        }

        ddt.log( "_isTrigger(): Not a zero width space. Is it a space character?" );
								
        if ( ! dom_node.nodeValue.charAt( caret_position ).match( /\s+/ ))
        {

          // it's not a space. If we are still looking for a trigger character check
          // to see if it is one. 

          if ( mode == 'looking_for_trigger' )
          {

            ddt.log( "_isTrigger(): checking '" + dom_node.nodeValue.charAt( caret_position ) + "' for trigger char" );

            if ( trigger_entry = this._isTriggerChar( dom_node.nodeValue.charAt( caret_position ) ))
            {

              ddt.log( "_isTrigger(): found trigger char '" + dom_node.nodeValue.charAt( caret_position ) + "' at caret_position '" + caret_position + "'" );

              mode = 'looking_for_space';

              // make life easy, remember where the trigger start character is. 
              // (It might be a number of zerowidth character before we find a space character or
              // beginning of the container)

              trigger_start.dom_node = dom_node;
              trigger_start.offset = caret_position;

              caret_position--;

              continue;

            }

            ddt.log( "_isTrigger(): '" + dom_node.nodeValue.charAt( caret_position) + "' not a trigger char." );

            caret_position--;

          }
          else
          {

            // we are looking for a space and it's not a zero width character or a space. Thus 
            // the trigger character has something else in front of it. Not a trigger boundary.
			
            ddt.log( "_isTrigger(): character before trigger is not a space" );

            trigger_entry = null;

            return false;

          }

        }	// end of if we found a non-space character.
        else
        {

          // we found a space. IF we were looking for a trigger then we end. 

          if ( mode == 'looking_for_trigger' )
          {

            ddt.log( "_isTrigger(): found a space instead of a trigger char" );

            return false;
          }

          ddt.log( "_isTrigger(): found a space. This is the start of a trigger" );

          break;	// out of while loop

        }

      }	// end of while loop.

      // --------------------------------
      // Arrive here when we've found the beginning of a trigger word taking multiple text nodes
      // and zero width space characters into account.

      ddt.log( "_isTriggerStart(): found a trigger." );

      trigger_entry.startOffset = trigger_start.offset + 1;
      trigger_entry.startNode = trigger_start.dom_node

      // _getWordEnd() expects the node and offset pointing at the trigger character.

      trigger_entry = this._getWordEnd( trigger_entry );

      // need to include the trigger character as well.

      trigger_entry.startOffset = trigger_start.offset;

      this.current_trigger = trigger_entry;

      return trigger_entry;

    },	
    _getWordEnd: function( word_entry )
    {
			
      var loop_brake = 200;
      var word = '';

      ddt.log( "_getWordEnd(): top with word_entry :", word_entry );

      var dom_node = word_entry.startNode;
      var caret_position = word_entry.startOffset;

      word_entry.word = '';

      ddt.log( "_getWordEnd(): at start of trigger word with caret_position '" + caret_position + "' and char '" + dom_node.nodeValue.charAt( caret_position ) + "' length '" + dom_node.nodeValue.length + "'" );

      while ( true )
      {

        // for when I make mistakes. avoids locking up the browser.

        if ( loop_brake-- <= 0 )
        {
          ddt.error( "_getWordEnd(): runaway loop" );

          return false;
        }

        ddt.log( "_getWordEnd(): Top of loop '" + caret_position + "'" );

        // can be 0 if we get a 0 length node 

        if ( caret_position >= dom_node.nodeValue.length )
        {

          if ( dom_node.nextSibling == null )
          {
					
            ddt.log( "_getWordEnd(): returning '" + word + "'" );

            word_entry.endNode = dom_node;
            word_entry.endOffset = caret_position - 1;
            word_entry.word = word;

            return word_entry;
					
          }

          if ( dom_node.nextSibling.nodeType != 3 )
          {

            ddt.log( "_getWordEnd(): nextSibling is NOT a text node. Returning '" + word + "'" );

            word_entry.endNode = dom_node;
            word_entry.endOffset = caret_position - 1;
            word_entry.word = word;

            return word_entry;
          }

          ddt.log( "_getWordEnd(): moving to next sibling of type '" + dom_node.nextSibling.nodeType + "' with length '" + dom_node.nextSibling.nodeValue.length + "' and value '" + dom_node.nextSibling.nodeValue + "'" );

          dom_node = dom_node.nextSibling;
          caret_position = 0;

          // occasionally at the end of a line, zero width text nodes show up in WebKit which are
          // apparently not selectable.
          //
          // FIXME: do these always show up at the end of a line? 

          if ( dom_node.nodeValue.length == 0 )
          {
						
            ddt.log( "_getWordEnd(): empty text node found." );

            continue;

          }

        }	// end of if we were at the end of a text node.

        // do we have a zero width space character? 

        if ( dom_node.nodeValue.charAt( caret_position ) == '\u200B' )
        {

          ddt.log( "_getWordEnd(): skipping zero width space character" );

          caret_position++;

          continue;
						
        }
				
        if ( ! dom_node.nodeValue.charAt( caret_position ).match( /\s+/ ) )
        {

          // it's not a zero width character or a space. add it to the trigger string.
			
          ddt.log( "_getWordEnd(): non-space, adding to string position '" + caret_position + "' char '" + dom_node.nodeValue.charAt( caret_position ) + "' node of length '" + dom_node.nodeValue.length + "':", dom_node );

          word += dom_node.nodeValue.charAt( caret_position );

          caret_position++;

        }
        else 
        {

          ddt.log( "_getWordEnd(): found a space. Returning '" + word + "'" );

          word_entry.endNode = dom_node;

          // current position is a space.

          word_entry.endOffset = caret_position - 1;
          word_entry.word = word;

          return word_entry;

        }

      }	// end of while loop.

    },	
    _isTriggerChar: function( schar )
    {

      for ( var i in this.options.triggers )
      {

        if ( schar == this.options.triggers[i].trigger )
        {

          // ddt.log( "_isTriggerChar(): found trigger char " + char );

          return this.options.triggers[i];
        }

      }

      return false;

    },	

    _getWord: function( dom_node, caret_position )
    {

      var loop_brake = 200;

      // used to return the word, and range.

      var word = {};

      // used to track if we found non-whitespace

      var found_char_flag = false;

      // remember that we can inconveniently have zerospace characters anywhere after
      // inserts of lines and objects and subsequent deletes.
      //
      // search backwards for a space.

      while ( true )
      {

        loop_brake--;

        if ( loop_brake <= 0 )
        {

          ddt.error( "_getWord(): runaway loop. braking" );

          return false;
        }

        if ( caret_position == -1 )
        {

          ddt.log( "_getWord(): top of loop, caret_position is '" + caret_position + "' previousSibling is:", dom_node.previousSibling );
		
          if ( dom_node.previousSibling == null )
          {

            // beginning of container means we've found a word boundary.
											
            ddt.log( "_getWord(): beginning of container found." );

            break;	// out of while loop 
					
          }

          if ( dom_node.previousSibling.nodeType != 3 )
          {

            // running into a different element also means a word boundary (likely a BR)

            ddt.log( "_getWord(): previousSibling is NOT a text node." );

            break;	// out of while loop 

          }

          dom_node = dom_node.previousSibling;

          caret_position = dom_node.nodeValue.length - 1;

          ddt.log( "_getWord(): moving to previousSibling length '" + caret_position + "'" );

          if ( caret_position == -1 )
          {

            // empty text nodes seem to be inserted by WebKit randomly.

            ddt.log( "_getWord(): zero length textnode encountered" );

            continue;

          }

        }	// end of if we are at the beginning of a textnode.

        // do we have a zero width space character? 

        if ( dom_node.nodeValue.charAt( caret_position ) == '\u200B' )
        {

          ddt.log( "_getWord(): skipping zero width space character" );

          caret_position--;

          continue;
						
        }

        ddt.log( "_getWord(): Not a zero width space. Is it a space character?" );
								
        if ( dom_node.nodeValue.charAt( caret_position ).match( /\s+/ ))
        {

          // we've found a space character and thus the beginning of a word.

          ddt.log( "_getWord(): found a space. This is the start of a word" );

          break;

        }
        else
        {
          // found a normal character

          found_char_flag = true;
        }


        ddt.log( "_getWord(): '" + dom_node.nodeValue.charAt( caret_position) + "' not a space." );

        caret_position--;

      }	// end of while loop.

      // --------------------------------
      // Arrive here when we've found the beginning of a word taking multiple text nodes
      // and zero width space characters into account.

      word.startNode = dom_node;

      // current char is a space. move past it.

      word.startOffset = caret_position + 1 ;

      // if we only matched whitespace, abort.

      if (! found_char_flag )
      {

        ddt.log( "_getWord(): only found whitespace" );

        return false;
      }

      ddt.log( "_getWord(): found a the beginning of a word, now searching for the end." );

      // _getWordEnd() expects the node and offset pointing at the beginning of the word.

      word = this._getWordEnd( word );

      return word;

    },	

    _checkForAdjacentObject: function( direction )
    {

      var location = {};
      var dom_node = null;
      var object = null;
	
      // when the editable div is first loaded and we have not yet 
      // clicked in the window, we may not have a current caret position.

      if ( ! ( location = this._getCaretPosition() ))
      {	
        return false;
      }

      dom_node = location.dom_node;

      ddt.log( "_checkForAdjacentObject(): looking in direction '" + direction + "' current node is :", dom_node );

      // SPECIAL CASE: if the node is clicked on by the mouse, we will, in FireFox, get the embedded object node.

      if ( this._isEmbeddedObject( dom_node ) )
      {
        ddt.log( "_checkForAdjacentObject(): current node is an object. returning" );

        return {
          dom_node: dom_node, 
          container_spanned: false, 
          preventDefault: true
        };
      }

      if (( location = this._treeWalker( location.dom_node, location.offset, direction )) == false )
      {
        ddt.log( "_checkForAdjacentObject(): none found" );
        return false;
      }

      ddt.log( "_checkForAdjacentObject(): _treeWalker returned: ", location );

      // SPECIAL HANDLING for Mozilla. If we get a _moz_dirty BR we consider ourselves NOT next to 
      // an object if we are looking to the right. (<BR> is essentially a stop character.)
      //
      // But we should look to the left. 

      if ( $( dom_node ).filter( '[_moz_dirty]' ).length != 0 )
      {

        if ( direction == 'left' )
        {

          // look left for an object. 

          if (( location = this._treeWalker( location.dom_node.previousSibling, location.offset, direction )) == false )
          {
            ddt.log( "_checkForAdjacentObject(): none found" );
            return false;
          }

        }
        else
        {
          ddt.log( "_checkForAdjacentObject(): current node is a moz_dirty filthy BR. don't look right." );

          return false;
        }
      }

      // if we are pointing at the beginning or end of a text node, we might be right beside
      // an embedded object. check the previous/next node

      if ( location.dom_node.nodeType == 3 )
      {

        ddt.log( "_checkForAdjacentObject(): _treeWalker returned a text node with offset '" + location.offset + "'" );

        if (( direction == 'left' ) && ( location.offset == 0 ))
        {

          if ( this._isEmbeddedObject( location.dom_node.previousSibling ) )
          {
            return {
              dom_node: location.dom_node.previousSibling, 
              container_spanned: ! location.checkForObjects, 
              preventDefault: location.preventDefault
            };
          }

        }
        else if (( direction == 'right' ) && ( location.offset == location.dom_node.nodeValue.length ))
        {

          if ( this._isEmbeddedObject( location.dom_node.nextSibling ) )
          {
            return {
              dom_node: location.dom_node.nextSibling, 
              container_spanned: ! location.checkForObjects, 
              preventDefault: location.preventDefault
            };
          }

        }

      }

      if ( ! this._isEmbeddedObject( location.dom_node ) )
      {
        return false;
      }

      return {
        dom_node: location.dom_node, 
        container_spanned: ! location.checkForObjects, 
        preventDefault: location.preventDefault
      };

    },	

    _clickedOnObject: function( dom_node )
    {

      ddt.log( "_clickedOnObject(): got current node : ", dom_node );

      // it's an object if it or some ancestor in the editable div has 
      // a data-value. 

      while ( $( dom_node ).attr( 'id' ) != this.element.attr( 'id' ) )
      {

        if ( this._isEmbeddedObject( dom_node ) )
        {

          ddt.log( "_clickedOnObject(): found object node '" + dom_node + "'" );

          return dom_node;
        }

        dom_node = dom_node.parentNode;

        ddt.log( "_clickedOnObject(): checking parent node : ", dom_node );

      }

      ddt.log( "_clickedOnObject(): user did not click on an object" );

      return false;

    },	

    deleteObject: function( dom_node )
    {

      ddt.log( "deleteObject(): top with node: ", dom_node );

      var sel = RANGE_HANDLER.getSelection();

      var parent = dom_node.parentNode;

      // originally the range included any surrounding zero width characters but no longer. 
      // It's better to have too many than not enough.

      var range = this._getObjectRange( dom_node );

      ddt.log( "deleteObject(): range to delete is : ", range );

      range.deleteContents();
      range.collapse( true );

      sel.removeAllRanges();
      sel.addRange( range );

      // was this object the only remaining object in our parent? 

      if (( $( parent ).attr( 'id' ) != this.element.attr( 'id' ) ) &&
        ( parent.childNodes.length == 0 ))
        {

        ddt.log( "deleteObject(): last element of container deleted. Deleting container." );

        range.setStartBefore( parent );
        range.setEndAfter( parent );
        range.deleteContents();
        range.collapse( true );

        sel.removeAllRanges();
        sel.addRange( range );

      }

      this._saveRange();

      return;

    },	

    _moveCaret: function( dom_node, caret_position, direction )
    {

      var loop_count = 0;
      var location = {};

      ddt.log( "_moveCaret(): top with direction '" + direction + "' with node: ", dom_node );

      if (( location = this._treeWalker( dom_node, caret_position, direction )) == false )
      {
        ddt.log( "_moveCaret(): _treeWalker returned false" );

        return false;
      }

      ddt.log( "_moveCaret(): _treeWalker() returned location: ", location );

      // do we have an object?

      if ( location.type == 'object' )
      {

        // position the caret before or behind the object. 
        //
        // NOTE: in this scenario we've just moved the caret towards the object,
        // so we want to stop at the object and not jump over it.

        if ( direction == 'left' )
        {
          this._setCaretPositionRelative( location.dom_node, 'after' );
        }
        else
        {
          this._setCaretPositionRelative( location.dom_node, 'before' );
        }

        return location;

      }

      // special handling if we were in a container and just stepped out.
			
      if ( location.type == 'container' )
      {
				
        ddt.log( "_moveCaret(): we were in a container and have stepped out. Selecting '" + direction + "' side" );

        if ( direction == 'left' )
        {

          // we may have a text node, a container, or some other element as our previousSibling. 
          // If it's a container, we want to select the last child in it. 

          if (( location.dom_node.previousSibling.nodeName == 'DIV' ) ||
            ( location.dom_node.previousSibling.nodeName == 'P' ))
            {

            // guard against an empty container

            if ( location.dom_node.previousSibling.childNodes.length == 0 )
            {
              ddt.error( "_moveCaret(): empty container previousSibling" );
              return false;
            }

            ddt.log( "_moveCaret(): moving into container: '" + location.dom_node.previousSibling + "' from '" + location.dom_node + "'" );

            this._setCaretPositionRelative( location.dom_node.previousSibling, 'end' );

            location.dom_node = location.dom_node.previousSibling.childNodes[ location.dom_node.previousSibling.childNodes.length - 1 ];

            return location;

          }	// end of if we had a container.

          // if it's a textnode, to work around issues in FireFox, we want to select the end
          // of the textnode. 

          if ( location.dom_node.previousSibling.nodeType == 3 )
          {

            ddt.log( "_moveCaret(): previousSibling is a textnode" );

            location.dom_node = location.dom_node.previousSibling;

            // Chrome may cause us grief with a 0 width text node here. 

            if	( location.dom_node.nodeValue.length == 0 )
            {

              // FIXME: not sure what to do in this case, if it ever occurs.

              ddt.error( "_moveCaret(): previousSibling is a zero length textnode." );
              return false;
            }

            this._setCaretPositionRelative( location.dom_node, 'end' );

            return location;

          }	// end of if we had a text node.

          location.dom_node = location.dom_node.previousSibling;

          // some other element. 

          ddt.log( "_moveCaret(): moving before element '" + location.dom_node + "'" );

          this._setCaretPositionRelative( location.dom_node, 'before' );

          return location;

        }
        else
        {

          // FIXME: Moving to the right seems less problematic across the board.

          // to avoid errors when we right arrowing at end the of the editable div.

          if ( location.dom_node.nextSibling == null )
          {

            ddt.log( "_moveCaret(): nextSibling is null in container. setting position 'after' with node:", location.dom_node );

            this._setCaretPositionRelative( location.dom_node, 'after' );
            return location;
          }

          // we may have some normal node like a text node or we may have a container 
          // as our nextSibling. We can't select the container, we want to select the
          // end of it. 

          if (( location.dom_node.nextSibling.nodeName == 'DIV' ) ||
            ( location.dom_node.nextSibling.nodeName == 'P' ))
            {

            // guard against an empty container

            if ( location.dom_node.nextSibling.childNodes.length == 0 )
            {
              ddt.error( "_moveCaret(): empty container nextSibling" );
              return false;
            }

            location.dom_node = location.dom_node.nextSibling.childNodes[ 0 ];

          }

          this._setCaretPositionRelative( location.dom_node, 'after' );

        }

        return location;
								
      }	// end of if we came out of a container.

      // special handling if we just stepped into a container. WebKit is fond of adding zero 
      // width text nodes at the ends of containers.

      if ( location.type == 'child' )
      {

        ddt.log( "_moveCaret(): handling special stepping into child case" );

        // If we're moving into a text node at the beginning of a container

        if ( location.dom_node.nodeType == 3 ) 
        {

          // FIXME: kept here for posterity as a reminder that we can get 0 length nodes
          // here.

          if ( location.dom_node.nodeValue.length == 0 )
          {
            ddt.log( "_moveCaret(): zero width text node. selecting it." );
					
            this._selectTextNode( location.dom_node, 0 );

            return location;
          }

          ddt.log( "_moveCaret(): text node child with some content. selecting it" );

          this._selectTextNode( location.dom_node, 0 );

          // since this is a text node we want to prevent the default action from happening
          // otherwise we move one character too far to the right. see _onKeyDown(). Since
          // we are a location type of 'child' onKeyDown will not jump over any adjacent objects.

          return location;
        }
															
        // since we just jumped into a container presumably, we don't want to 
        // jump over any objects we happen to be next to.

        return location;

      }	// end of if we moved into a child container.
										
      // text node.

      if ( location.dom_node.nodeType == 3 )
      {

        ddt.log( "_moveCaret(): selecting text node for direction '" + direction + "' :", location );

        this._selectTextNode( location.dom_node, location.offset );

        return location;
      }

      // For mozilla, move the cursor to the far side of a <BR _moz_dirty=""> tag. 

      if ( location.dom_node.nodeName == 'BR' )
      {

        ddt.log( "_moveCaret(): we have a BR" );

        // is this a mozilla _moz_dirty BR? 

        if ( $( location.dom_node ).filter( '[_moz_dirty]' ).length != 0 )
        {

          // we've come up on a BR in mozilla, which is used to mark the end
          // of lines.

          if ( direction == 'left' )
          {

            ddt.log( "_moveCaret(): moving to left side of _moz_dirty BR starting with location:", location );

            //						location = this._moveCaret( location.dom_node.previousSibling, -1, 'left' );

            location.type = '_moz_dirty';
            location.preventDefault = true;

            ddt.log( "_moveCaret(): location returned after BR is: ", location );

            this._setCaretPositionRelative( location.dom_node, 'before' );

            return location;

          }
          else
          {
            ddt.log( "_moveCaret(): moving to right side of _moz_dirty BR" );

            // just move to the right of the BR which represents moving down to the 
            // new line. Any additional zero space characters will be consumed the next
            // time the user presses arrow keys

            this._setCaretPositionRelative( location.dom_node, 'after' );

            return location;

          }

        }	// end of if we had a _moz_dirty BR.

        // some other normal BR.

        ddt.log( "_moveCaret(): normal BR" );

        if ( direction == 'left' )
        {
          this._setCaretPositionRelative( location.dom_node, 'before' );
        }
        else
        {
          this._setCaretPositionRelative( location.dom_node, 'after' );
        }

      }

      return location;

    },	
    _backspaceZeroSpace: function()
    {

      var dom_node = null;
      var delete_flag = false;
      var runaway_brake = 0;
      var caret_position = null;
      var location = {};
      var start_location = {};
      var end_location = {};
      var sel = null;
      var range = null;

      location = this._getCaretPosition();

      dom_node = location.dom_node;

      ddt.log( "_backspaceZeroSpace(): current dom node is :", dom_node );

      if (( dom_node.nodeType != 3 ) && ( dom_node.nodeName != 'BR' ))
      {
				
        ddt.log( "_backspaceZeroSpace(): backspacing over a NON-BR '" + dom_node.nodeName + "' node" );

        return false;
      }
			
      // BR's have to be handled specially.
			
      if ( dom_node.nodeName == 'BR' )
      {

        ddt.log( "_backspaceZeroSpace(): backspacing over BR" );

        // Lines are separated by BR's in Mozilla with the moz_dirty attribute. If we encounter
        // one we consider it a stop word. DO NOT delete any objects in front of it.
        //
        // see _onKeyDown BACKSPACE

        if ( $( dom_node ).filter( '[_moz_dirty]' ).length != 0 )
        {
          ddt.log( "_backspaceZeroSpace(): moz_dirty filthy BR encountered. Stop word" );

          return 'stop';
        }

        // depends on what we find in front of the BR. Could be a textnode, could be some
        // other element or might be the beginning of a container.

        if ( dom_node.previousSibling == null )
        {

          ddt.log( "_backspaceZeroSpace(): beginning of container" );

          this._setCaretPositionRelative( dom_node, 'before' );

          $( dom_node ).remove();

          // tell the caller we moved the cursor.

          return true;
        }

        if ( dom_node.previousSibling.nodeType != 3 )
        {

          ddt.log( "_backspaceZeroSpace(): previous element is NOT a textnode :", dom_node.previousSibling );

          this._setCaretPositionRelative( dom_node, 'before' );

          $( dom_node ).remove();

          // tell the caller we moved the cursor. 

          return true;

        }

        // arrive here if we have a text node. move the cursor to the end of the text node.

        this._setCaretPositionRelative( dom_node.previousSibling, 'end' );
        $( dom_node ).remove();

      }

      // arrive here if we have a text node for an end point.

      end_location = this._getCaretPosition();

      if (( start_location = this._walkTextNode( end_location.dom_node, end_location.offset, 'left' )) == false )
      {
        ddt.error( "_backspaceZeroSpace(): walkTextNode return false" );

        return false;
      }

      ddt.log( "_backspaceZeroSpace(): got start_location: ", start_location );

      sel = RANGE_HANDLER.getSelection();
      range = CREATERANGE_HANDLER.createRange();

      // the start_location may be an element (object) which we do not want to delete here.
      // this method should just delete the zerospace chars.

      if ( start_location.dom_node.nodeType != 3 )
      {
        range.setStartAfter( start_location.dom_node );
      }
      else
      {
        range.setStart( start_location.dom_node, start_location.offset );
      }

      range.setEnd( end_location.dom_node, end_location.offset );
      range.deleteContents();

      sel.removeAllRanges();
      sel.addRange( range );

      this._saveRange();

      return true;

    },	

    _deleteZeroSpace: function()
    {

      var dom_node = null;
      var delete_flag = false;
      var runaway_brake = 0;
      var caret_position = null;
      var location = {};
      var start_location = {};
      var end_location = {};
      var sel = null;
      var range = null;

      start_location = this._getCaretPosition();

      ddt.log( "_deleteZeroSpace(): current dom node is :", start_location.dom_node );

      // Unlike backspace, all we care about are non-printing text nodes.

      if ( start_location.dom_node.nodeType != 3 )
      {
				
        ddt.log( "_deleteZeroSpace(): deleting NON-TEXT '" + start_location.dom_node.nodeName + "' node" );

        return false;
      }
			
      // we have a text node.

      if (( end_location = this._walkTextNode( start_location.dom_node, start_location.offset, 'right' )) == false )
      {
        ddt.error( "_deleteZeroSpace(): walkTextNode return false" );

        return false;
      }

      ddt.log( "_deleteZeroSpace(): got start and end_location: ", start_location, end_location );

      // if we did not skip over any non-printing characters, do nothing.
      // NOTE: === comparison here checks to see if the two nodes are the same node, not just the same type.

      if (( start_location.dom_node === end_location.dom_node ) && ( start_location.offset == end_location.offset ))
      {

        ddt.log( "_deleteZeroSpace(): _walkTextNode() did not move cursor" );

        return false;
      }

      sel = RANGE_HANDLER.getSelection();
      range = CREATERANGE_HANDLER.createRange();

      range.setStart( start_location.dom_node, start_location.offset );

      // the end_location may be an element (object) which we do not want to delete here.
      // this method should just delete the zerospace chars.

      if ( end_location.dom_node.nodeType != 3 )
      {
        ddt.log( "_deleteZeroSpace(): setting end of range before dom_node" );
        range.setEndBefore( end_location.dom_node );
      }
      else
      {
        ddt.log( "_deleteZeroSpace(): setting end of range at offset '" + end_location.offset + "'" );
        range.setEnd( end_location.dom_node, end_location.offset );
      }

      range.deleteContents();

      sel.removeAllRanges();
      sel.addRange( range );

      this._saveRange();

      return true;

    },	
		
    _treeWalker: function( dom_node, caret_position, direction )
    {

      var location = {
        type: ''
      };
      var loop_brake = 100;

      // used to note if we jumped a container at any point and thereby have to prevent the default
      // action on any event callback. 

      var preventDefault_flag = false;
			
      // keeps track of whether or not we've spanned a container. 
			
      var container_spanned_flag = false;
									
      ddt.log( "_treeWalker(): top searching '" + direction + "' caret_position '" + caret_position + "' current node: ", dom_node );

      while ( dom_node != null )
      {

        // to avoid those times I make a mistake and lock the browser.

        if ( loop_brake-- <= 0 )
        {
          ddt.error( "_treeWalker(): runaway loop" );
          return false;
        }

        // if we have a text node that contains anything other than zero width space characters we 
        // return it.

        if ( dom_node.nodeType == 3 )
        {

          ddt.log( "_treeWalker(): we have a text node with contents '" + dom_node.nodeValue + "' and caret_position '" + caret_position + "'" );

          if (( location = this._walkTextNode( dom_node, caret_position, direction )) == false )
          {
            ddt.error( "_treeWalker(): walkTextNode returned false" );
            return false;
          }

          ddt.log( "_treeWalker(): walkTextNode() returned: ", location );

          // there are several cases: 
          //
          //	.	either end of the editable div
          //	.	either end of a container
          //	.	adjacent to an object
          //	.	in a text node with non-zero-width space character.
					
          switch ( location.type )
          {
							
            case 'text':
						
              ddt.log( "_treeWalker(): _walkTextNode() returned a textnode" );

              // if we end up with a zero width textnode, loop around and do it again.

              if ( location.dom_node.nodeValue.length > 0 )
              {
                ddt.log( "_treeWalker(): _walkTextNode() returned a normal text node" );

                location.preventDefault = preventDefault_flag;

                return location;
              }

              ddt.log( "_treeWalker(): _walkTextNode() returned a 0 width text node" );
								
              if ( direction == 'left' )
              {
                dom_node = location.dom_node.previousSibling;
              }
              else
              {
                dom_node = location.dom_node.nextSibling;
              }

              caret_position = -1;

              continue;

              break;

            case 'element':
            case 'object':

              // likely a BR or DIV.

              ddt.log( "_treeWalker(): _walkTextNode() returned an element or object" );

              dom_node = location.dom_node;
              caret_position = -1;

              break;

            // FIXME: should be named 'container_end' or similar. 

            case 'container':

              // we walked to the end of a text node and encountered a container. 
              // We need to move to the previous or next sibling of the container.

              ddt.log( "_treeWalker() _walkTextNode() encountered the end of a container. need to step out." );

              preventDefault_flag = true;
              container_spanned_flag = true;

              caret_position = -1;

              dom_node = location.dom_node;

              break;

            case 'end':

              // end of the editable div reached.

              ddt.log( "_treeWalker(): at end of editable div" );

              return false;

              break;

          }	// end of switching over _walkTextNode() reponses

        }	// end of we were dealing with a text node.
											
        if ( this._isEmbeddedObject( dom_node ))
        {

          // we have an embedded object.

          ddt.log( "_treeWalker(): we have found an object node: ", dom_node );

          // IMPORTANT: in Webkit if we disable the default behavior on moving right, it won't jump
          // over the object correctly.
          //
          // if we have spanned containers we do not want to highlight the object (i.e. we can be
          // called from highlightObject() 'left' or 'right', and only want to highlight the objects
          // if we have not spanned a container.)

          var check_for_objects = true;

          if ( container_spanned_flag )
          {
            check_for_objects = false;
          }

          return {
            dom_node: dom_node, 
            offset: -1, 
            type: 'object', 
            preventDefault: preventDefault_flag, 
            checkForObjects: check_for_objects
          };

        }
				
        if ( dom_node.nodeName == 'BR' )
        {

          // we have a BR. skip over it unless it's a moz_dirty which indicates a
          // stopping point.

          ddt.log( "_treeWalker(): we have a BR" );

          if ( $( dom_node ).filter( '[_moz_dirty]' ).length != 0 )
          {

            ddt.log( "_treeWalker(): we have a _moz_dirty BR. stopping" );

            return {
              dom_node: dom_node, 
              offset: -1, 
              type: 'element', 
              preventDefault: preventDefault_flag, 
              checkForObjects: false
            };

          }

          // FIXME: Need to check this in all places where we can get BR's. If we have
          // a few BR's in a row the user may press keys multiple times to get over them.

          return {
            dom_node: dom_node, 
            offset: -1, 
            type: 'element', 
            preventDefault: preventDefault_flag, 
            checkForObjects: false
          };

        }

        // special handling for a container we encounter. We dive into the container and start
        // from the end based on direction, but only if we haven't just stepped out of the same
        // container as a result of _walkTextNode(). See above. Ugly, I know.

        if (( location.type != 'container' ) &&
          (( dom_node.nodeName == 'DIV' ) ||
            ( dom_node.nodeName == 'SPAN' ) ||
            ( dom_node.nodeName == 'P' )))
            {

          ddt.log( "_treeWalker(): we have found a container of type '" + dom_node.nodeName + "' :", dom_node );

          preventDefault_flag = true;
          container_spanned_flag = true;

          // we have encountered a container at one end or the other.
          //
          // e.g. something like <div>text<div><span>object</span></div>
          //
          // or <div>text</div><div>text</div>
          //
          // or <div><div><div>test</div></div></div>
          // 
          // in the last case we want to jump into the div's to the end of 'test'
          // regardless of the level of nesting. Chrome, when editing and merging/splitting
          // lines seems to like nesting div's. I haven't had much luck reliably modifying
          // the markup (seems to confuse Chrome), so I'm just treating multiply nested
          // div's as a single newline. (which is who it's actually rendered in the 
          // contenteditable div anyway).
          //
          // However, the container boundary is a stop IF the last/first child node
          // is itself NOT a container (i.e. stop on objects, text but not
          // nested divs or p's)
          // 
          // So, if there is some node we can step into at the end of the container
          // we return the container and let our caller step into it. Otherwise we loop
          //
          // does our container have children?

          if ( dom_node.childNodes.length == 0 )
          {
            ddt.log( "_treeWalker(): container with 0 children. adding text node and returning." );

            var textnode = this._insertEmptyNode( dom_node, 'child' );
            return {
              dom_node: textnode, 
              offset: 0, 
              type: 'child', 
              preventDefault: preventDefault_flag, 
              checkForObjects: false
            };
          }

          // inspect the child node at the end.

          var child_node = null;

          if ( direction == 'left' )
          {

            ddt.log( "_treeWalker(): LEFT: getting child of container at position '" + (dom_node.childNodes.length - 1) + "'" );

            child_node = dom_node.childNodes[ dom_node.childNodes.length - 1 ];
          }
          else
          {
            child_node = dom_node.childNodes[ 0 ];
          }

          // guard against the possibility of some other unexpected markup making it 
          // into the DIV.
          //
          // FIXME: in the future we'll probably want to support all kinds of markup to make
          // the editable area more expressive.

          if (( child_node.nodeType != 3 ) &&
            ( ! this._isEmbeddedObject( child_node )) &&
            ( child_node.nodeName != 'DIV' ) &&
            ( child_node.nodeName != 'P' ) &&
            ( child_node.nodeName != 'BR' ))
            {
            ddt.log( "_treeWalker(): returning a container that has an element child node '" + child_node.nodeName + "'" );

            return {
              dom_node: dom_node, 
              offset: -1, 
              type: 'child', 
              preventDefault: preventDefault_flag, 
              checkForObjects: false
            };
          }

          // HACK: special check for Chrome. Make sure to stop if we enter into a DIV containing
          // just a zero width space character. Otherwise after the user presses enter a bunch of times
          // we may end up skipping lines on moving LEFT or RIGHT.

          if (( child_node.nodeType == 3 ) &&
            ( child_node.nodeValue.match( /^[\u200B]+$/ ) != null ))
            {
            ddt.log( "_treeWalker(): webKit hack. empty DIV with zero width space. Stopping" );

            return {
              dom_node: child_node, 
              offset: 0, 
              type: 'child', 
              preventDefault: preventDefault_flag, 
              checkForObjects: false
            };

          }

          ddt.log( "_treeWalker(): bottom of loop, container with '" + dom_node.childNodes.length + "' children, child at end of container is:", child_node );

          dom_node = child_node;

          continue;

        }	// end of if we found a container

        // this is not the node you are looking for, move along.
        //
        // Check the previous or next node. If we are at the end of the current
        // container and our parent is not the editable node. move up a level.

        if ((( direction == 'left' ) && ( dom_node.previousSibling == null )) ||
          (( direction == 'right' ) && ( dom_node.nextSibling == null )))
          {

          ddt.log( "_treeWalker(): we have come to an end of a container." );

          // if our parent is the contenteditable div, then we have come to the end and have not 
          // found what we were looking for.

          if ( $( dom_node.parentNode).attr( 'id' ) == this.element.attr( 'id' ) )
          {

            ddt.log( "_treeWalker(): we have come to the beginning or end of the editable div and not found a stopping point" );

            return false;

          }

          // otherwise move up a level and continue walking.

          dom_node = dom_node.parentNode;

          ddt.log( "_treeWalker(): moving up to parent level: ", dom_node );

        }	// end of if we are at the beginning or end of a container

        if ( direction == 'left' )
        {
          dom_node = dom_node.previousSibling;
        }
        else
        {
          dom_node = dom_node.nextSibling;
        }

        // we use location.type as an ugly flag;

        location.type = '';

      }	// end of while loop

      ddt.error( "_treeWalker(): outside of while." );

      return false;

    },	
    _walkTextNode: function( dom_node, caret_position, direction )
    {

      var loop_brake = 200;

      // guard against getting some other node.

      if ( dom_node.nodeType != 3 )
      {
        ddt.error( "_walkTextNode(): called with a '" + dom_node.nodeName + "' node" );
        return false;
      }

      // -1 is a hack to indicate starting from one end or the other depending on direction.

      if ( caret_position == -1 ) 
      {
        if ( direction == 'left' )
        {
          caret_position = dom_node.nodeValue.length;
        }
        else
        {
          caret_position = 0;
        }
      }

      ddt.log( "_walkTextNode(): direction '" + direction + "' starting with char is '" + dom_node.nodeValue.charAt( caret_position ) + "' at position '" + caret_position + "' length '" + dom_node.nodeValue.length + "' parent is :", dom_node.parentNode );

      // remember that we can inconveniently have zerospace characters anywhere after
      // inserts of lines and objects and subsequent deletes.

      switch ( direction )
      {

        case 'left':

          var check_siblings = false;

          if ( caret_position == 0 )
          {
            check_siblings = true;
          }

          while ( true )
          {

            ddt.log( "_walkTextNode(): top of left loop, char is '" + dom_node.nodeValue.charAt( caret_position ) + "' at position '" + caret_position + "' length '" + dom_node.nodeValue.length + "'" );

            // for when I make a mistake and loop endlessly.

            if ( loop_brake-- <= 0 )
            {
              ddt.error( "_walkTextNode(): runaway loop. braking" );
              return false;
            }

            // if the caret is pointing at the first character of the string
            // i.e. offset 0, check the previous node.

            if ( check_siblings )
            {
		
              check_siblings = false;

              ddt.log( "_walkTextNode(): checking previousSibling" );

              // are we at the beginning of a container? 

              if ( dom_node.previousSibling == null )
              {
                ddt.log( "_walkTextNode(): beginning of container found." );

                // we might be at the beginning of the editable div.

                if ( $( dom_node.parentNode ).attr( 'id' ) == this.element.attr( 'id' ) )
                {
                  ddt.log( "_walkTextNode(): end of editable div" );

                  return {
                    dom_node: dom_node, 
                    offset: 0, 
                    type: 'end', 
                    preventDefault: false, 
                    checkForObjects: true
                  };
                }

                // we are at the beginning of a container. 
                // The caller will check the container's previousSibling.

                ddt.log( "_walkTextNode(): stepping out of a container to parent:", dom_node.parentNode );

                return {
                  dom_node: dom_node.parentNode, 
                  offset: -1, 
                  type: 'container', 
                  preventDefault: false, 
                  checkForObjects: true
                };

              }	// end of if we reached the beginning of a container.

              // is the sibling not a text node?

              if ( dom_node.previousSibling.nodeType != 3 )
              {

                ddt.log( "_walkTextNode(): previousSibling is NOT a text node:", dom_node.previousSibling );

                dom_node = dom_node.previousSibling;

                return {
                  dom_node: dom_node, 
                  offset: -1, 
                  type: 'element', 
                  preventDefault: false, 
                  checkForObjects: true
                };

              }

              dom_node = dom_node.previousSibling;

              // we always look to the left of the caret. Start past the end of the string.

              if (( caret_position = dom_node.nodeValue.length ) == 0 )
              {

                // should not happen, no?

                ddt.log( "_walkTextNode(): zero length textnode encountered" );

                caret_position = 0;

                check_siblings = true;

                continue;

              }

            }	// end of if we were at the beginning of a text node.

            // the range startOffset returns the offset of the character to the
            // the right of the caret. So, when searching left, we need to examine
            // the previous character. Hence the -1 here.

            if ( dom_node.nodeValue.charAt( caret_position - 1 ) != '\u200B' )
            {

              ddt.log( "_walkTextNode(): Not a zero width space at position '" + caret_position + "' is a '" + dom_node.nodeValue.charCodeAt( caret_position - 1 ) + "'" );

              return {
                dom_node: dom_node, 
                offset: caret_position, 
                type: 'text', 
                preventDefault: false, 
                checkForObjects: false
              };

            }

            ddt.log( "_walkTextNode(): found a zero width space char at offset '" + ( caret_position - 1 ) + "'" );

            caret_position--;

            if ( caret_position == 0 )
            {
              check_siblings = true;
            }

          }	// end of while loop.

          ddt.error( "_walkTextNode(): bottom of left loop" );

          return false;

          break;

        // ----------------------------------------------------------

        case 'right':

          while ( true )
          {

            // for when I make a mistake.

            if ( loop_brake-- <= 0 )
            {

              ddt.error( "_walkTextNode(): runaway loop. braking" );

              return false;
            }

            // we search to the end of the string.

            if ( caret_position == dom_node.nodeValue.length )
            {

              ddt.log( "_walkTextNode(): we are at the end of the string." );
		
              if ( dom_node.nextSibling == null )
              {
					
                ddt.log( "_walkTextNode(): end of container found :", dom_node );

                // we might be at the end of the editable div.

                if ( $( dom_node.parentNode ).attr( 'id' ) == this.element.attr( 'id' ) )
                {
                  ddt.log( "_walkTextNode(): end of editable div" );

                  return {
                    dom_node: dom_node, 
                    offset: caret_position, 
                    type: 'end', 
                    preventDefault: false, 
                    checkForObjects: false
                  };
                }

                // we are at the end of a container. The call will check the
                // container's nextSibling.

                ddt.log( "_walkTextNode(): stepping out of a container" );

                // There is an edge case which is namely we do not want to step out of 
                // the contenteditable div.

                if ( $( dom_node.parentNode ).attr( 'id' ) == this.element.attr( 'id' ) )
                {

                  ddt.log( "_walkTextNode(): attempted to step out of editable div." );

                  // we'll insert a textnode in this case and return that. 

                  var textnode = this._insertEmptyNode( dom_node, 'after' );

                  return {
                    dom_node: textnode, 
                    offset: 0, 
                    type: 'end', 
                    preventDefault: false, 
                    checkForObjects: true
                  };

                }

                return {
                  dom_node: dom_node.parentNode, 
                  offset: -1, 
                  type: 'container', 
                  preventDefault: false, 
                  checkForObjects: true
                };

              }

              // we may encounter an element, likely a BR.

              if ( dom_node.nextSibling.nodeType != 3 )
              {

                ddt.log( "_walkTextNode(): nextSibling is NOT a text node:", dom_node.nextSibling );

                dom_node = dom_node.nextSibling;

                return {
                  dom_node: dom_node, 
                  offset: -1, 
                  type: 'element', 
                  preventDefault: false, 
                  checkForObjects: true
                };

              }

              ddt.log( "_walkTextNode(): moving to nextSibling" );

              dom_node = dom_node.nextSibling;

              caret_position = 0;

              // this should not happen, no?

              if ( dom_node.nodeValue.length == 0 )
              {

                // should not happen, no?

                ddt.log( "_walkTextNode(): zero length textnode encountered" );

                continue;

              }

            }

            if ( dom_node.nodeValue.charAt( caret_position ) != '\u200B' )
            {

              ddt.log( "_walkTextNode(): Not a zero width space at position '" + caret_position + "'. Found '" + dom_node.nodeValue.charCodeAt( caret_position ) + "'" );

              return {
                dom_node: dom_node, 
                offset: caret_position, 
                type: 'text', 
                preventDefault: false, 
                checkForObjects: true
              };

            }

            caret_position++;

          }	// end of while loop.

          ddt.error( "_walkTextNode(): bottom of right loop :", dom_node );
		
          return false;

      }	// end of switch

    },	

    _highlightObject: function()
    {

      var object = false;

      ddt.log( "_highlightObject(): top with this", this );

      this._unHighlightObjects( this.element );

      // if we have moved next to an embedded object, such that another
      // backspace will delete the object (in _onKeyDown()), highlight the
      // object. (or if we're in front of it and a delete will delete it.)

      ddt.log( "_highlightObject(): checking for prev object" );

      if ( object = this._checkForAdjacentObject( 'left' ) )
      {

        ddt.log( "_highlightObject(): check for object to left returned:", object );

        if ( ! object.container_spanned )
        {
          $( object.dom_node ).addClass( 'highlight' );
        }

      }

      ddt.log( "_highlightObject(): checking for next object" );

      if ( object = this._checkForAdjacentObject( 'right' ) )
      {

        ddt.log( "_highlightObject(): check for object to right returned:", object );

        if ( ! object.container_spanned )
        {
          $( object.dom_node ).addClass( 'highlight' );
        }

      }

    },	

    _unHighlightObjects: function( object )
    {

      // now remove the highlight class from any of the other objects. 
      //
      // Objects are always one level under the contenteditable div. 
      //
      // We are just interested in elements in this case, ok to loop over children instead of childNodes

      var rich_textarea = this;

      object.children().each( function( index )
      {

        // we only recurse into elements that are NOT one of our objects, identified by 
        // the data-value attribute.

        if ( rich_textarea._isEmbeddedObject( $(this).get(0) ) )
        {

          // ddt.log( "found element with data value: ", $(this).attr( 'data-value' ) );

          $(this).removeClass( 'highlight' );

          return;

        }

        // ddt.log( "_unHighlightObjects(): not an embedded object. checking for children of '" + $(this).prop( 'nodeName' ) + "' with '" + $(this).children().length + "'" );
								
        if ( $( this ).children().length > 0 )
        {

          // ddt.log( "unHighlightObjects(): recursing into '" + $( this ).prop( 'nodeName' ) + "'" );

          rich_textarea._unHighlightObjects( $( this ) );

        }

      });

    // ddt.log( "_unHightlightObjects(): end" );


    },

    _selectTextNode: function( text_node, offset )
    {

      // if we do not receive a textnode it's an error

      if ( text_node.nodeType != 3 )
      {

        ddt.error( "_selectTextNode(): ERROR - node of type '" + text_node.nodeName + "' received." );

        return false;

      }

      ddt.log( "_selectTextNode(): setting offset '" + offset + "' in text node of length '" + text_node.nodeValue.length + "'" );

      var selection = RANGE_HANDLER.getSelection();

      var range = CREATERANGE_HANDLER.createRange();

      range.setStart( text_node, offset );
      range.setEnd( text_node, offset );
      range.collapse(true);

      selection.removeAllRanges();
      selection.addRange( range );

      this._saveRange( range );

    },

   
    _setCaretPositionRelative: function( dom_node, position )
    {
      var sel = RANGE_HANDLER.getSelection();

      var range = null;

      ddt.log( "_setCaretPositionRelative(): moving '" + position + "' relative to :", dom_node );

      if ( dom_node.previousSibling != null )
      {
        ddt.log( "_setCaretPositionRelative(): with previousSibling: ", dom_node.previousSibling );
      }

      if ( this._isEmbeddedObject( dom_node ) )
      {

        ddt.log( "_setCaretPositionRelative(): setting caret position '" + position + "' relative to an embedded object. getting object range." );

        // it's an object so get the range with potentially wrapping
        // zero width space text nodes around it.

        range = this._getObjectRange( dom_node );

        ddt.log( "_setCaretPositionRelative(): got object range: ", range );

        switch ( position )
        {
					
          case 'before':
          case 'beginning':
			
            ddt.log( "_setCaretPositionRelative(): collapsing to start of range around object" );

            range.collapse( true );

            break;

          case 'after':
          case 'end':

            ddt.log( "_setCaretPositionRelative(): collapsing to end of range around object" );

            range.collapse( false );

            break;

        }

        sel.removeAllRanges()
        sel.addRange( range );

        this._saveRange();

        return;

      }	// end of if we were selecting an inserted object.

      // selecting a single cursor position.

      range = sel.getRangeAt(0);

      switch ( position )
      {

        case 'before':

          // for a BR use the zero width space character trick and set the range explicitly.

          if ( dom_node.nodeName == 'BR' )
          {
            ddt.log( "_setCaretPositionRelative(): 'before' with a 'BR'" );

            var textnode = this._insertEmptyNode( dom_node, 'before' );

            this._setCaretPositionRelative( textnode, 'end' );

            return;
          }

          range.setStartBefore( dom_node );
          range.setEndBefore( dom_node );

          range.collapse( true );

          sel.removeAllRanges()
          sel.addRange( range );

          this._saveRange();

          var caret = this._getCaretPosition();

          break;

        case 'after':

          // for a BR use the zero width space character trick and set the range explicitly.

          if ( dom_node.nodeName == 'BR' )
          {
            ddt.log( "_setCaretPositionRelative(): 'after' with a 'BR'" );

            // FIXME: this is probably a hack. For a BR, position the cursor
            // before the BR and let the browser move the cursor to the other
            // side of the BR on it's own. 

            range.setStartBefore( dom_node );
            range.setEndBefore( dom_node );

            range.collapse( false );
        	
            sel.removeAllRanges()
            sel.addRange( range );

            this._saveRange();

            break;
          }

          range.setStartAfter( dom_node );
          range.setEndAfter( dom_node );

          range.collapse( false );

          sel.removeAllRanges()
          sel.addRange( range );

          this._saveRange();

          break;

        case 'beginning':

          // we only want this to work on text nodes

          if ( dom_node.nodeType != 3 )
          {
            ddt.error( "_setCaretPositionRelative(): 'beginning not on a text node: ", dom_node );
            return;
          }

          range.setStart( dom_node, 0 );
          range.setEnd( dom_node, 0 );

          range.collapse( false );

          sel.removeAllRanges()
          sel.addRange( range );

          this._saveRange();

          break;

        case 'end' :

          // we only want this to work on text nodes

          if ( dom_node.nodeType != 3 )
          {
            ddt.error( "_setCaretPositionRelative(): 'end not on a text node: ", dom_node );
            return;
          }

          // 'end' is really one character past the end of the node per 
          // docs: http://help.dottoro.com/ljlmndqh.php The range end is
          // one character past the end of the range. 

          range.setStart( dom_node, dom_node.nodeValue.length );
          range.setEnd( dom_node, dom_node.nodeValue.length );

          range.collapse( false );

          sel.removeAllRanges()
          sel.addRange( range );

          this._saveRange();

          break;

      }

      // HACK: with nested DIV's in Mozilla it's possible to move the cursor to the end position
      // no-man's land in the editable DIV. In this case we'll insert a zero-width char at the end
      // and adjust the range accordingly.

      if (( range.startContainer.nodeName == 'DIV' ) &&
        ( $( range.startContainer ).attr( 'id' ) == this.element.attr( 'id' ) ) &&
        ( range.startOffset == range.startContainer.childNodes.length ))
        {

        ddt.error( "_setCaretPositionRelative(): attempted to break out of div." );

        var textnode = this._insertEmptyNode( range.startContainer, 'child' );

        this._selectTextNode( textnode, 0 );

      }

      ddt.log( "_setCaretPositionRelative(): result range is: ", range );

    },	

    
    _getObjectRange: function( dom_node )
    {

      ddt.log( "_getObjectRange(): top" );

      var sel = RANGE_HANDLER.getSelection();
      var range = sel.getRangeAt(0);

      var tmp_range = null;
      var offset = 0;

      if ( ! sel.rangeCount ) 
      {
        ddt.error( "_getObjectRange(): NO RANGE. UNABLE TO MOVE CARET." );

        return;
      }

      // ------------------------ BEFORE OBJECT ----------------------------------------

      ddt.log( "_getObjectRange(): getting position 'before' node" );

      // is there a node? 

      if ( dom_node.previousSibling == null )
      {

        ddt.log( "_getObjectRange(): BEFORE: No sibling node to the left" );

        // this "should not" happen but still does occasionally if the user manages to 
        // delete a last remaining zero width space. 
        //
        // we'll patch it up in this case, otherwise there's no getting the selection before
        // the object.

        var textnode = this._insertEmptyNode( dom_node, 'before' );

        range.setStart( textnode, 0 );

      }
      else if ( dom_node.previousSibling.nodeType != 3 )
      {

        // this is probably due to the browser adding some markup by itself. 

        ddt.log( "_getObjectRange(): BEFORE: sibling to the left NOT A TEXT NODE, it's a '" + dom_node.previousSibling.nodeName + "'" );

        var textnode = this._insertEmptyNode( dom_node, 'before' );

        range.setStart( textnode, 0 );

      }
      else if ( dom_node.previousSibling.nodeValue == null )
      {

        // this shouldn't happen, no? 

        ddt.log( "_getObjectRange(): BEFORE: sibling to the left is an EMPTY/NULL TextNode" );

        var textnode = this._insertEmptyNode( dom_node, 'before' );

        range.setStart( textnode, 0 );

      }
      else if ( dom_node.previousSibling.nodeValue.length == 0 )
      {

        // this shouldn't happen, no?

        ddt.log( "_getObjectRange(): BEFORE: sibling to the left is a 0 length TextNode" );

        var textnode = this._insertEmptyNode( dom_node, 'before' );

        range.setStart( textnode, 0 );

      }
      else 
      {

        // we have a text node with some content. This makes the area before the object 
        // selectable. 

        ddt.log( "_getObjectRange(): existing text node. setting range to start before object" );

        range.setStart( dom_node.previousSibling, dom_node.previousSibling.nodeValue.length );

      }	// end of else we had a textnode containing characters.

      // ------------------------- AFTER OBJECT -----------------------------------

      tmp_range = CREATERANGE_HANDLER.createRange();

      // is there a node? 

      if ( dom_node.nextSibling == null )
      {

        ddt.log( "_getObjectRange(): AFTER: No sibling node to the right" );

        // this "should not" happen but still does occasionally if the user manages to 
        // backspace over the last remaining zero width space. 
        //
        // we'll patch it up in this case, otherwise there's no getting the selection after
        // the object.

        var textnode = this._insertEmptyNode( dom_node, 'after' );

        range.setEnd( textnode, 1 );

      }                      
      else if ( dom_node.nextSibling.nodeType != 3 )
      {

        // this is probably due to the browser adding some markup by itself. 

        ddt.log( "_getObjectRange(): AFTER: sibling to the right NOT A TEXT NODE, it's a '" + dom_node.nextSibling.nodeName + "'" );

        var textnode = this._insertEmptyNode( dom_node, 'after' );

        range.setEnd( textnode, 1 );

      }
      else if ( dom_node.nextSibling.nodeValue == null )
      {

        // this shouldn't happen, no? 

        ddt.log( "_getObjectRange(): AFTER: sibling to the right is an EMPTY/NULL TextNode" );

        var textnode = this._insertEmptyNode( dom_node, 'after' );

        range.setEnd( textnode, 1 );

      }
      else if ( dom_node.nextSibling.nodeValue.length == 0 )
      {

        // this shouldn't happen, no?

        ddt.log( "_getObjectRange(): AFTER: sibling to the right is a 0 length TextNode" );

        var textnode = this._insertEmptyNode( dom_node, 'after' );

        range.setEnd( textnode, 1 );

      }
      else 
      {

        // we have a text node with some content.

        ddt.log( "_getObjectRange(): existing text node." );

        // If this is NOT a zero width text node, add one in for good measure.
        //
        // FIXME: I've been having quite a bit of trouble with moving over objects
        // that are <SPAN>s in FireFox vs. Chrome. When event.preventDefault() is set,
        // Chrome doesn't move the cursor out of the span when moving right. If event.preventDefault
        // is not sent, Chrome works but FireFox sends the caret one too many characters to the right
        // in _moveCaret().
        //
        // Making sure there is an empty text node no matter what seems to solve the situation for
        // both browsers. Yes, this polutes a bunch of extra characters but the user gets the behavior
        // they would expect.

        if ( dom_node.nextSibling.nodeValue != '\u200B' )
        {
          var textnode = this._insertEmptyNode( dom_node, 'after' );
          range.setEnd( textnode, 1 );
        }
        else
        {
          range.setEnd( dom_node.nextSibling, 1 );
        }

      }	// end of else we had a textnode containing characters.

      return range;

    },	

    _getCaretPosition: function()
    {

      var dom_node = null;
      var text_node = null;
      var embedded_object = null;

      var sel = RANGE_HANDLER.getSelection();

      // This may fail if nothing is selected.

      try {
        var range = RANGE_HANDLER.getSelection().getRangeAt(0);

      }
      catch( err )
      {
        ddt.log( "_getCaretPosition(): unable to get position " + err );
        return false;
      }

      ddt.log( "_getCaretPosition(): top" );

      if ( range.collapsed == false )
      {
        ddt.log( "_getCaretPosition(): multi-char selection" );
        return false;
      }

      dom_node = range.startContainer;

      // ddt.log( "_getCaretPosition(): got container from range of type '" + dom_node.nodeName + "'" );

      // The problem is, the user may have selected a node INSIDE an embedded object. 
      //
      // If it weren't for the webkit bug of having contenteditable=false items on a line interfering
      // with deletions this would be so much easier. 
      //
      // in Mozilla it seems like an endless cat and mouse game to avoid getting "inside" 
      // an embedded object 
      //
      // Check to see if we are inside an object, regardless of our node type.

      // ddt.log( "_getCaretPosition(): checking to see if this node is or is inside an embedded object :", dom_node );

      if ( embedded_object = this._isEmbeddedObject( dom_node ) )
      {

        ddt.log( "_getCaretPosition(): we have an embedded object" );

        return {
          dom_node: embedded_object, 
          offset: -1
        };

      }	// end of if we were inside an embedded object.

      // ddt.log( "_getCaretPosition(): not an embedded object" );

      // do we have a text node?
			
      if ( dom_node.nodeType == 3 )
      {

        // ddt.log( "_getCaretPosition(): we have a text node of length '" + dom_node.nodeValue.length + "' startOffset '" + range.startOffset + "'" );

        return {
          dom_node: dom_node, 
          offset: range.startOffset
        };

      }

      // If the node is a container, we need to use the offset to get the current element in the
      // container (which should NOT be a text node). This can happen if:
      //
      //		. we are at the end of a container.
      //		. we are between elements.
      //		. we are between a BR and the beginning of a container.

      if (( $( dom_node ).attr( 'id' ) == this.element.attr( 'id' ) ) ||
        ( dom_node.nodeName == 'DIV' ) ||
        ( dom_node.nodeName == 'P' ))
        {

        ddt.log( "_getCaretPosition(): Got a container (DIV/P) as a parent. We are possibly next to a BR or at the end. startOffset is '" + range.startOffset + "'" );

        // This should not occur, but have been encountered an empty container? 

        if ( dom_node.childNodes.length == 0 )
        {

          ddt.log( "_getCaretPosition(): EMPTY CONTAINER! Adding empty text node." );

          text_node = this._insertEmptyNode( dom_node, 'child' );

          this._selectTextNode( text_node, 0 );

          return {
            dom_node: text_node, 
            offset: 0
          };

        }	// end of if we had an empty container.

        // are we at the end of the container? It's possible to get a startOffset that is past the
        // range of childNodes meaning we are at the end of a container. In WebKit browsers this is
        // "unselectable no-man's land"

        if ( range.startOffset >= dom_node.childNodes.length )
        {

          ddt.log( "_getCaretPosition(): We are at the end of a container which is unselectable in webKit browsers" );

          // insert a zero space node here and return that. 

          text_node = this._insertEmptyNode( dom_node, 'after' );

          this._selectTextNode( text_node, 1 );

          return {
            dom_node: text_node, 
            offset: 1
          };

        }	// end of if we were at the end of a container.

        dom_node = dom_node.childNodes[ range.startOffset ];

        // ddt.log( "_getCaretPosition(): element at offset is :'" + dom_node.nodeName + "'" );

        // this should never be a textnode, correct? If it's a textnode then it should have been
        // returned as the container.

        if ( dom_node.nodeType == 3 )
        {

          // FIXME: If this happens we don't know where in the node to position the caret. 

          ddt.error( "_getCaretPosition(): THIS SHOULD NOT HAPPEN. TEXTNODE RETURNED AS CONTAINER OFFSET" );

          this._selectTextNode( dom_node, 0 );

          return {
            dom_node: dom_node, 
            offset: 0
          };

        }

      }	// end of if we had a container.

      // this should never be a text node, correct?

      return {
        dom_node: dom_node, 
        offset: -1
      };

    },	
		 
    _insertSelection: function( trigger, selection )
    {

      ddt.log( "_insertSelection(): deleting trigger word based on trigger: ", trigger, " with currentRange ", this.currentRange );

      this.replaceWord( trigger, selection.content, selection.value );

      // FIXME: There's a bug in jquery.ui.autocomplete having to do with up and down
      // arrows in FireFox not working. autocomplete intercepts and disables some keypresses.
      // so that Firefox works I've modified ui.autocomplete to not disable keypresses but it
      // looks like (hypothesis) that becuase of that onEnter is getting fired even when a
      // selection menu item is selected. 
      //
      // Let onKeyUp know not to handle this enter press.

      this.selectionEntered = true;

    },	

    replaceWord: function( word_entry, content, data_value )
    {

      var sel = RANGE_HANDLER.getSelection();
      var range = CREATERANGE_HANDLER.createRange();

      ddt.log( "replaceWord(): deleting word: ", word_entry );

      // However, because the fact the WebKit does not merge adjacent textnodes the 
      // trigger word may span multiple nodes (and have zero width space characters in between)
      // the trigger sent to us contains the complete range.

      range.setStart( word_entry.startNode, word_entry.startOffset );

      // from the docs: The end position of a Range is the first position in the DOM hierarchy that is after the Range.

      range.setEnd( word_entry.endNode, word_entry.endOffset + 1 );

      range.deleteContents();

      this._saveRange( range );

      // FIXME: I do not understand why but if I apply this here it causes one extra space to get consumed
      // when the object is inserted. This makes no sense to me. Clearly I'm missing something.

      //			sel.removeAllRanges();
      //			sel.addRange( range );

      this.insertObject( content, data_value );

    }, 

   

    _insertEmptyNode: function( dom_node, direction, force )
    {

      if ( typeof( force ) == 'undefined' )
      {
        force = false;
      }

      var text_node = document.createTextNode( '\u200B' );

      switch ( direction )
      {

        case 'before':

          // FIXME: we seem to be getting back 0 length text nodes in webkit sometimes. Not sure why.

          if (( ! force ) &&
            ( dom_node.previousSibling != null ) && 
            ( dom_node.previousSibling.nodeType == 3 ) &&
            ( dom_node.previousSibling.nodeValue.length > 0 ))
            {
            ddt.log( "_insertEmptyNode(): there's already a text node before this node" );
            return dom_node.previousSibling;
          }

          $( text_node ).insertBefore( dom_node );

          break;

        case 'after':

          if (( ! force ) &&
            ( dom_node.nextSibling != null ) && 
            ( dom_node.nextSibling.nodeType == 3 ) &&
            ( dom_node.nextSibling.nodeValue.length > 0 ))
            {
            ddt.log( "_insertEmptyNode(): there's already a text node after this node" );
            return dom_node.nextSibling;
          }

          $( text_node ).insertAfter( dom_node );

          break;

        case 'child':

          // is the last child of this node already a text node?

          if (( dom_node.childNodes.length != 0 ) && ( dom_node.childNodes[ dom_node.childNodes.length - 1 ].nodeType == 3 ))
          {
            ddt.log( "_insertEmptyNode(): there's already a text node at the end of this container." );
            return dom_node.childNodes[ dom_node.childNodes.length - 1 ];
          }

          $( text_node ).appendTo( dom_node );

          break;

        default:

          ddt.error( "_insertEmptyNode(): Invalid direction supplied '" + direction + "'" );

          break;

      }

      return text_node;

    },

   

    _checkSibling: function( dom_node, direction )
    {

      var sibling = null;

      ddt.log( "_checkSibling(): dom_node is :", dom_node );

      if ( direction == 'prev' )
      {
        sibling = dom_node.previousSibling;
      }
      else
      {
        sibling = dom_node.nextSibling;
      }

      ddt.log( "_checkSibling(): sibling is :", sibling );

      // are we at the beginning or end of a container? 

      if ( sibling == null ) 
      {
        ddt.log( "_checkSibling(): sibling is null." );

        return;
      }
			
      if ( this._isEmbeddedObject( sibling ) )
      {

        ddt.log( "_checkSibling(): object sibling" );

        // make certain the sibling is wrapped in textnodes

        this._insertEmptyNode( sibling, 'before' );
        this._insertEmptyNode( sibling, 'after' );

        return;
      }

      // it might be a BR

      if ( sibling.nodeName == 'BR' )
      {

        ddt.log( "_checkSibling(): sibling is a BR" );

        // make certain the sibling is wrapped in textnodes

        this._insertEmptyNode( sibling, 'before' );
        this._insertEmptyNode( sibling, 'after' );

        return;
      }

      // is it not a container?

      if (( sibling.nodeName != 'SPAN' ) && 
        ( sibling.nodeName != 'DIV' ) &&
        ( sibling.nodeName != 'P' ))
        {
        ddt.log( "_checkSibling(): sibling is not a container: '" + sibling.nodeName + "'" );

        return;
      }

      // is it an empty container? 

      if ( sibling.childNodes.length == 0 )
      {

        ddt.log( "_checkSibling(): empty container. adding textnode" );

        this._insertEmptyNode( sibling, 'child' );

        return;

      }

      // does it just container a BR? (WebKit)

      if (( sibling.childNodes.length == 1 ) && ( sibling.childNodes[ 0 ].nodeName == 'BR' ))
      {

        ddt.log( "_checkSibling(): DIV containing just a BR found. Adding a zero width char." );

        var tmp_node = sibling.childNodes[ 0 ];

        this._insertEmptyNode( tmp_node, 'before' );

        $( tmp_node ).remove()
				
        return;

      }

      // we have a container and it has child nodes. Insert textnodes at the beginning
      // and end.

      this._insertEmptyNode( sibling.childNodes[ 0 ], 'before' );
      this._insertEmptyNode( sibling.childNodes[ sibling.childNodes.length - 1 ], 'after' );

      return;

    },	

    _isEmbeddedObject: function( dom_node )
    {

      var embedded_object = null;

      if ( dom_node == null )
      {
        ddt.log( "_isEmbeddedObject(): NULL node passsed in" );
        return false;
      }

      // ddt.log( "_isEmbeddedObject(): inspecting node :", dom_node );

      if ( $( dom_node ).attr( 'data-value' ) != null )
      {
        return dom_node;
      }

      // ddt.log( "_isEmbeddedObject(): not a TOP LEVEL embedded object node. Is one of our parents?" );

      // we may be any kind of node inside an embedded object 

      return $( dom_node ).parents( '[data-value]' ).get(0);

    },	

    insertObject: function( content, value )
    {

      ddt.log( "insertObject(): top with content '" + content + "' and value '" + value + "'" );

      // this method is often invoked from the 'outside' and as such the 
      // editable div loses focus which messes up the works.

      this.element.focus();

      ddt.log( "insertObject(): after focus - currentRange is ", this.currentRange );

      // we may have lost focus so restore the range we saved after
      // each keypress. However, we also need to take into the account
      // that the user may not have clicked in the editable div at all.

      if ( this.currentRange === false )
      {

        ddt.log( "insertObject(): currentRange is false" );

        // insert a blank text node in the div

        var textnode = this._insertEmptyNode( this.element.get(0), 'child' );

        this._selectTextNode( textnode, 1 );

      // _selectTextNode() calls _saveRange() which affects currentRange. 
      // I know, ugly side-effect.

      }

      var sel = RANGE_HANDLER.getSelection();
      var range = this.currentRange;

      sel.removeAllRanges();
      sel.addRange( range );

      var caret = null;

      // for some reason for a range of content returned from the server
      // this results in an expression error. 
      //
      // var node = $( content );
      //
      // Trim the content just in case we have a few whitespace characters leading or following.

      var tempDiv = document.createElement('div');
      tempDiv.innerHTML = content.replace(/^[\s\u200B]+|[\s\u200B]+$/g,"");

      // make sure not to include the wrapping temporary div. We make the
      // assumption here that content is wrapped in some single container tag,
      // either a div or a span.

      node = $( tempDiv ).contents();

      ddt.log( "insertObject(): node is ", node );

      node.attr( 'data-value', value );

      //			FIXME: This breaks webkit browsers. If you press DELETE or backspace such that
      //			two lines are joined, the latter of which has a contenteditable=false item on it
      //			everything from the item to the end of the line will be unceremoniously deleted.
      //
      //			node.attr( 'contenteditable', 'false' );

      // to avoid the mess that results when trying to get a range on the 
      // empty/non-existent text node between two objects when they are placed next to 
      // one another, we insert zero width space characters as needed. This 
      // can then be selected in a range allowing us to move the cursor to the space
      // between the objects, notably in WebKit browsers.
      //
      // Without some kind of character between the <div>'s, the selection will
      // jump to the nearest inside one of the divs. (Which, if you think about it, makes
      // sense from the perspective of a user at the keyboard. You don't want to have to 
      // move the arrow key over invisible entities ...)
      //
      // The same problem occurs when an object is placed at the very beginning or very 
      // end of the contenteditable div.
      //
      // Unfortunately, zero width space characters do take up a keyboard arrow press,
      // i.e. if you arrow over such a character the cursor doesn't move but you have to 
      // press the arrow key once for each such character which is confusing. This is
      // addressed in the _onKeyDown() handler. We move the cursor over them.

      // The approach is to add the object then check to see if we have sibling objects 
      // before or after us. If not, we add them.

      var dom_node = node.get(0);

      range.insertNode( node.get(0) );
      range.setStartAfter( node.get(0) );
      range.collapse( true );

      sel.removeAllRanges();
      sel.addRange(range);

      ddt.log( "insertObject(): previousSibling is : ", dom_node.previousSibling );

      // check siblings before and after us, if any. 
      //
      // And, in Chome and possibly other browsers, if this is the first element there is, 
      // an entirely empty text node is insert at the first position. 

      ddt.log( "insertObject(): inserting zero width node before selection" );

      // FIXME: Not sure why, but if I don't force the inclusion of empty nodes even if
      // the object is surrounded by text nodes selections break. wtf? (i.e. without this
      // inserting object into the middle of text lines fails in Webkit)

      var textnode = this._insertEmptyNode( dom_node, 'before', true );

      //			this._selectTextNode( textnode, 1 );

      // if there is no sibling after us or if it's not a text node, add a zero width space.

      ddt.log( "insertObject(): inserting zero width node after selection" );

      var textnode2 = this._insertEmptyNode( dom_node, 'after', true );

      // FIXME: if this is 0, in Chrome it selects a point in the span.

      this._selectTextNode( textnode2, 1 );

    },	
    getTextContent: function()
    {

      ddt.log( "getTextContent(): top" );

      // this.element is a jQuery object.

      var content = this._getTextWithLineBreaks( this.element.get(0).childNodes );

      // strip out all the zero width space characers.

      ddt.log( "getTextcontent(): content length before replace is: '" + content.length + "'" );

      content = content.replace( /[\u200B]/gm, '' );

      if ( content.match( /[\u200B]/ ) != null )
      {
        ddt.error( "getTextContent(): zero width chars in content" );
      }

      ddt.log( "getTextcontent(): content length after replace is: '" + content.length + "'" );

      return content;
    },

   

    _getTextWithLineBreaks: function( elems )
    {

      // list of elements used by various browsers to 
      // mark newlines in a contenteditable section.

      var break_tags = [ 'BR', 'DIV', 'P' ];

      var text_string = "";
      var elem;

      ddt.log( "elems ", elems );

      for ( var i = 0; elems[i]; i++ )
      {

        elem = elems[i];

        ddt.log( "elem is '" + elem.nodeName + "'" );

        if ( this._isEmbeddedObject( elem ) )
        {

          ddt.log( "embedded object found" );
          text_string += "[o=" + $( elem ).attr( 'data-value' ) + "]";

          continue;
        }

        if (( elem.nodeType == 3 ) || ( elem.nodeType == 4 ))
        {

          ddt.log( "text or cdata found" );

          // text or cdata node

          // first strip out any newlines that might be in the content. Might be from
          // copy paste, etc. The only newlines present in the output should be ones that
          // reflect the formatting the user sees in the browser.

          text_string += elem.nodeValue.replace( /[\n]/gm, '' );

        }
        else if ( jQuery.inArray( elem.nodeName, break_tags ) != -1 )
        {

          ddt.log( "break_tag found, adding newline" );
          text_string += "\n";
        }
        else
        {
          ddt.log( "other" );
        }

        if ( elem.nodeType !== 8 ) // comment node
        {
          text_string += this._getTextWithLineBreaks( elem.childNodes );
        }

      }

      return text_string;

    },	

    clear: function()
    {
      this.element.empty();
    },

   

    focus: function()
    {

      var sel = RANGE_HANDLER.getSelection();
      var range = CREATERANGE_HANDLER.createRange();

      range.setStart( this.element.get(0), 0 );

      this.element.focus();

    }

  });

})(jQuery);
