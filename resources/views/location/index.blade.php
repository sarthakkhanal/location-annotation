@extends('location.layouts.app')
@section('css')
<style>
.navbar-nav li {
  margin-top: 8px;
  margin-bottom: 8px;
}
.tabs-container {
  margin-top: 100px;
}
.parent-border, .child-border {
  border: 1px solid #CCC;
  border-radius: 4px;
  padding: 15px;
  margin-bottom: 15px;
}
.btn-circle.btn-lg {
  width: 50px;
  height: 50px;
  padding: 10px 16px;
  font-size: 18px;
  line-height: 1.33;
  border-radius: 25px;
}
.context-menu {
  box-shadow: 0 4px 5px 3px rgba(0, 0, 0, 0.2);
  display: none;
  position: absolute;
  z-index: 10;
  background-color:#fff;
  padding: 2px;
}
.context-menu--active {
  display: block;
}
.context-menu__items {
    list-style: none;
    padding: 10px 0;
  }
.context-menu__item{
      font-weight: 500;
      font-size: 14px;
      padding: 10px 40px 10px 20px;
      cursor: pointer;

      &:hover {
        background: rgba(0, 0, 0, 0.2);
      }
    }
</style>
@endsection
@section('content')
<nav class="context-menu navbar">
  <ul class="context-menu__items">
    <li class="context-menu__item">
      <a href="#" class="context-menu__link" data-action="add">
        <i class="glyphicon glyphicon-plus"></i> Add location
      </a>
    </li>

  </ul>
</nav>
<div class="container" style="margin:0;width:100%">
  <div class="row tabs-container" style="margin-top:10px">
      <div class="col-sm-offset-0 col-sm-12">
          <div class="panel with-nav-tabs panel-default">
              <div class="panel-heading">
                  Filter
              </div>
              <div class="panel-body">
                <form class="form-inline">
                  <input type="hidden" name="search" value="search">
                  <div class="form-group">
                    <label for="id">Tweet id</label>
                    <input class="form-control" id="id" name="tweet_id">
                  </div>

                  {{--<div class="radio">
                    <label >Annotated?</label>
                    <label class="radio-inline"><input type="radio" name="annotated" value="1">Yes</label>
                    <label class="radio-inline"><input type="radio" name="annotated" checked value="0">No</label>
                  </div>--}}
                  <button type="submit" class="btn btn-default">Search</button>
                  <a href="?" class="btn btn-default">Clear</a>
                </form>
              </div>
          </div>
      </div>
  </div>
  <div class="row tabs-container" style="margin-top:10px">
      <div class="col-sm-offset-0 col-sm-12">
          <div class="panel with-nav-tabs panel-default">
              <div class="panel-heading">
                  Tweets
              </div>
              <div class="panel-body">
                <table class="table">
                  <col width="40%"/>
                  <col width="5%"/>
                  <col width="55%"/>
                  <thead>
                    <tr>

                      <th scope="col">Tweet text</th>
                      <th scope="col"></th>
                      <th scope="col" style="text-align: center">Annotation</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($tweets as $tweet)

                      <tr>

                        <td class="tweet_text" data-id="{{$tweet->id}}">{{$tweet->text}}</td>
                        <td>@if($tweet->locationRelevance)
                              @if($tweet->locationRelevance->relevance==0)
                                <span class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-alert"></i></span>
                              @endif
                            @endif
                        </td>
                        <td>
                          <form class="form-inline form_{{$tweet->id}}" id="form_{{$tweet->id}}" method="post"  onsubmit="return validateForm(this);">
                            <input type="hidden" name="tweet_id" value="{{$tweet->id}}">
                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                            <div class="parent-border col-sm-offset-0 col-sm-12" style="display:none">

                                <div class="form-group">


                                    <div class="col-sm-8" style="padding:2px">
                                        <input type="text" style="width:100%" class="form-control" id="location_mention_{{$tweet->id}}"
                                               placeholder="Enter location"
                                               data-name="location[]"
                                               unique="location" >
                                    </div>
                                </div>
                                <div class="form-group">


                                    <div class="col-sm-3" style="padding:2px">
                                        <select class="form-control" style="width:100%" id="location_type_{{$tweet->id}}" data-name="location_type[]">
                                            <option value="" disabled selected>Select location Type</option>
                                            @foreach($categories as $c)
                                              <option>{{$c}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-0 col-sm-1" style="padding:2px">
                                        <button type="button" class="deleteRow btn btn-danger btn-circle btn-xs" onclick="deleteRow(this)"><i
                                                class="glyphicon glyphicon glyphicon-trash"></i></button>
                                    </div>
                                  </div>

                            </div>
                            <div class="form-container">
                            </div>
                            {{--<div class="form-group">

                                <div class="col-sm-3" style="padding:2px;width:100%">
                                    <select class="form-control" style="width:100%" id="humanitarian_{{$tweet->id}}" name="humanitarian">
                                        <option value="" disabled  @if($tweet->locationRelevance) @if(!$tweet->locationRelevance->humanitarian) selected @endif @else selected @endif >Select humanitarian category</option>
                                        @foreach($humanitarian as $h)
                                          <option @if($tweet->locationRelevance) @if($tweet->locationRelevance->humanitarian == $h) selected @endif @endif value="{{$h}}">Humanitarian: {{$h}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">


                                <div class="col-sm-3" style="padding:2px;width:100%">
                                  <label >Eye witness?</label>
                                  <label class="radio-inline"><input type="radio" name="eye_witness" @if($tweet->locationRelevance) @if($tweet->locationRelevance->eye_witness) checked @endif @endif  value="1">Yes</label>
                                  <label class="radio-inline"><input type="radio" name="eye_witness" @if($tweet->locationRelevance) @if(!$tweet->locationRelevance->eye_witness) checked @endif @else checked @endif value="0">No</label>
                                </div>
                            </div>--}}
                            <div class="form-group" style="float:right">
                                <div class="col-sm-offset-0 col-sm-1">
                                  @if($tweet->locationRelevance)
                                      @if($tweet->locationRelevance->relevance==1)
                                        <a type="button" class="irrelevant btn  btn-md btn-primary" title="Irrelevant" href="?relevant=false&tweet_id={{$tweet->id}}"><i
                                            class="glyphicon glyphicon-remove"> No location</i></a>
                                      @endif
                                  @else
                                    <a type="button" class="irrelevant btn  btn-md btn-primary" title="Irrelevant" href="?relevant=false&tweet_id={{$tweet->id}}"><i
                                      class="glyphicon glyphicon-remove"> No location</i></a>
                                  @endif
                                </div>
                            </div>
                            <div class="form-group" style="float:right">
                              <div class="col-sm-offset-0 col-sm-1">
                                  <button class="form-save btn btn-md btn-success"><i
                                          class="glyphicon glyphicon-floppy-disk">Save</i></button>
                                </div>
                            </div>
                            <div class="form-group" style="float:right">
                                <div class="col-sm-offset-0 col-sm-1">
                                    <button type="button" class="addRow btn  btn-md btn-primary" onclick="newRow(this)"><i
                                            class="glyphicon glyphicon-plus">Add</i></button>
                                </div>
                            </div>

                          </form>
                      </td>
                      </tr>
                      @if($tweet->locationLabels->count()>0)
                        <script>

                          addRows("{{$tweet->id}}",{!! json_encode($tweet->prepareForForm()) !!});
                        </script>
                      @endif
                    @endforeach
                  </tbody>
                </table>

                {{ $tweets->links() }}
              </div>
          </div>
      </div>
  </div>
@endsection
@section('js')
<script>
$(document).ready(function() {

  "use strict";

  ///////////////////////////////////////
  ///////////////////////////////////////
  //
  // H E L P E R    F U N C T I O N S
  //
  ///////////////////////////////////////
  ///////////////////////////////////////

  function clickInsideElement( e, className ) {
    var el = e.srcElement || e.target;

    if ( el.classList.contains(className) ) {
      return el;
    } else {
      while ( el = el.parentNode ) {
        if ( el.classList && el.classList.contains(className) ) {
          return el;
        }
      }
    }

    return false;
  }



  function getPosition(e) {
    var posx = 0;
    var posy = 0;

    if (!e) var e = window.event;

    if (e.pageX || e.pageY) {
      posx = e.pageX;
      posy = e.pageY;
    } else if (e.clientX || e.clientY) {
      posx = e.clientX + document.body.scrollLeft +
                         document.documentElement.scrollLeft;
      posy = e.clientY + document.body.scrollTop +
                         document.documentElement.scrollTop;
    }

    return {
      x: posx,
      y: posy
    }
  }
  function positionMenu(e) {
    clickCoords = getPosition(e);
    clickCoordsX = clickCoords.x;
    clickCoordsY = clickCoords.y;

    menuWidth = menu.offsetWidth + 4;
    menuHeight = menu.offsetHeight + 4;

    windowWidth = window.innerWidth;
    windowHeight = window.innerHeight;

    if ( (windowWidth - clickCoordsX) < menuWidth ) {
      menu.style.left = windowWidth - menuWidth + "px";
    } else {
      menu.style.left = clickCoordsX + "px";
    }

    if ( (windowHeight - clickCoordsY) < menuHeight ) {
      menu.style.top = windowHeight - menuHeight + "px";
    } else {
      menu.style.top = clickCoordsY + "px";
    }
  }
  function highlight(e) {
    var text = "";
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (document.selection && document.selection.type != "Control") {
        text = document.selection.createRange().text;
    }


    //right mouse clicked
    if(event.which==3){
      return (text);
    }
    else return null;
  }

  ///////////////////////////////////////
  ///////////////////////////////////////
  //
  // C O R E    F U N C T I O N S
  //
  ///////////////////////////////////////
  ///////////////////////////////////////



  function resizeListener() {
    window.onresize = function(e) {
      toggleMenuOff();
    };
  }
  /**
   * Variables.
   */
   var contextMenuClassName = "context-menu";
  var contextMenuItemClassName = "context-menu__item";
  var contextMenuLinkClassName = "context-menu__link";
  var contextMenuActive = "context-menu--active";
  var taskItemClassName = 'tweet_text';
  var menu = document.querySelector(".context-menu");
  var menuState = 0;
  var menuPosition;
  var menuPositionX;
  var menuPositionY;
  var menuWidth;
  var menuHeight;
  var windowWidth;
  var windowHeight;
  var clickCoords;
  var clickCoordsX;
  var clickCoordsY;
  var taskItemInContext;
  var selection=null;
  /**
   * Initialise our application's code.
   */
  function init() {
    contextListener();
    clickListener();
    keyupListener();
    resizeListener();
  }

  /**
   * Listens for contextmenu events.
   */
   function contextListener() {
     document.addEventListener( "contextmenu", function(e) {
       taskItemInContext = clickInsideElement( e, taskItemClassName );
       if ( clickInsideElement( e, taskItemClassName ) ) {
         e.preventDefault();
         selection=highlight(e);
         if(selection.length){
           selection=selection.trim();
           toggleMenuOn();
           positionMenu(e);

         }else{
           taskItemInContext = null;
           toggleMenuOff();
         }

       } else {
         taskItemInContext = null;
         toggleMenuOff();

       }
     });
   }

  /**
   * Listens for click events.
   */
   function clickListener() {
     document.addEventListener( "click", function(e) {
       var clickeElIsLink = clickInsideElement( e, contextMenuLinkClassName );
       if ( clickeElIsLink ) {
        e.preventDefault();
        menuItemListener( clickeElIsLink);
      } else {
         var button = e.which || e.button;
         if ( button === 1 ) {
           toggleMenuOff();
         }
      }
     });
   }

  /**
   * Listens for keyup events.
   */
   function keyupListener() {
     window.onkeyup = function(e) {
       if ( e.keyCode === 27 ) {
         toggleMenuOff();
       }
     }
   }

  /**
   * Turns the custom context menu on.
   */
  function toggleMenuOn() {
    if ( menuState !== 1 ) {
      menuState = 1;
      menu.classList.add( contextMenuActive );
    }
  }
  function toggleMenuOff() {
    if ( menuState !== 0 ) {
      menuState = 0;
      menu.classList.remove( contextMenuActive );
    }
    selection=null;
  }
  function menuItemListener( link) {
    var tweet_id=taskItemInContext.getAttribute("data-id");
    var action = link.getAttribute("data-action");
    if(selection.length){
      addRow(tweet_id,{
        "location":selection
      });
    }
    toggleMenuOff();
  }
  /**
   * Run the app.
   */
  init();

});


function highlight(e) {
  var text = "";
  if (window.getSelection) {
      text = window.getSelection().toString();
  } else if (document.selection && document.selection.type != "Control") {
      text = document.selection.createRange().text;
  }
}

(function($) {
  $.fn.closest_descendent = function(filter) {
      var $found = $(),
          $currentSet = this; // Current place
      while ($currentSet.length) {
          $found = $currentSet.filter(filter);
          if ($found.length) break;  // At least one match: break loop
          // Get all children of the current set
          $currentSet = $currentSet.children();
      }
      return $found.first(); // Return first match of the collection
  }
})(jQuery);

  function newRow(item) {

      var len = $(item).closest('.form-inline').find('.child-border').length;
      var parent = $(item).closest('.form-inline').find('.parent-border').clone();

      parent.find(':input').each(function (idx, ele) {
          //ele.name=ele.name.replace(/\[\d+\]/g,'['+len+']');
          $(ele).attr("required","true");
          $(ele).attr("pattern","\\s*\\S.*");
          $(ele).attr("title","Field must contain atleast one non whitespace character.");
          ele.name = $(ele).attr("data-name");
          ele.id = ele.id + "_"+len;
          ele.value = '';
      }).end().find('.form-group').toggle(true).end()
              .toggleClass('parent-border child-border').hide()
              .appendTo($(item).closest('.form-inline').find('.form-container')).slideDown();




  };
  function addRows(tweet_id,rows){
    rows.forEach(function(element){
      addRow(tweet_id,element);
    });
  }
  function addRow(tweet_id,data){
    var thisForm = $("#form_"+tweet_id);
    var len = thisForm.find('.child-border').length;
    var parent = thisForm.find('.parent-border').clone();

    parent.find(':input').each(function (idx, ele) {
        //ele.name=ele.name.replace(/\[\d+\]/g,'['+len+']');
        $(ele).attr("required","true");
        $(ele).attr("pattern","\\S(.*\\S)?");
        $(ele).attr("title","Field must contain atleast one non whitespace character.");
        ele.name = $(ele).attr("data-name");
        ele.id = ele.id + "_"+len;
        ele.value = data[ele.name.replace(/\[.*]/g,'')];

    }).end().find('.form-group').toggle(true).end()
            .toggleClass('parent-border child-border').hide()
            .appendTo(thisForm.find('.form-container')).slideDown();
  }

  function validateForm(form) {
      var jsonData = $(form)
              .find(':input:not(button)').get()
              .reduce(function (acc, ele) {
                if(ele.value.trim().length!=0) acc.push({"name":ele.name,"value":ele.value});
                return acc;
              }, []);
      var prefix = "location";
      var selector = jQuery.validator.format("[unique='{0}']", prefix);
      var values = new Array();
      $(selector).each(function(index, item) {
          if($(item).val().trim().length)
            values.push($(item).val().trim().toLowerCase());
      });

      if(hasDuplicates(values)){
        console.log("Duplicate location entries");
        alert("Duplicate location entries");
        return false;
      }



      return true;
  };
  function hasDuplicates(a) {
      var counts = [];
      for(var i = 0; i <= a.length; i++) {
          if(counts[a[i]] === undefined) {
              counts[a[i]] = 1;
          } else {
              return true;
          }
      }
      return false;
  }
  function deleteRow(item) {
      var jsonData = $(item).closest('.child-border, .parent-border')
              .find(':input:not(button)').get()
              .reduce(function (acc, ele) {$("#myform").validate();
                  acc.push({"name":ele.name,"value":ele.value});
                  return acc;
              }, []);
      $(item).closest('.child-border, .parent-border').remove();

  };
  $.validator.addMethod("unique", function(value, element, params) {
      var prefix = params;
      var selector = jQuery.validator.format("[name!='{0}'][unique='{1}']", element.name, prefix);
      var matches = new Array();
      $(selector).each(function(index, item) {
          if (value.trim().toLowerCase() == $(item).val().trim().toLowerCase()) {
              matches.push(item);
          }
      });

      return matches.length == 0;
  }, "Duplicate location entries.");

  $.validator.classRuleSettings.unique = {
      unique: true
  };


</script>
@endsection
