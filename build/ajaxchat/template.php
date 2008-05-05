<?php ?>

<script type="text/javascript" src="script/prototype162.js"></script>        
<script type="text/javascript"><!--//

// setting the baseuri for ajax calls, because sometimes it doesn't work.
var baseuri = "<?=PVars::getObj('env')->baseuri ?>";

//--------------- autoscroll -----------------------

var autoscroll_active = true;
function scroll_down() {
    if (!autoscroll_active) return;
    var chat_scroll_box = document.getElementById("chat_scroll_box");
    chat_scroll_box.scrollTop = chat_scroll_box.scrollHeight;
}
function on_manual_scroll() {
    var chat_scroll_box = $("chat_scroll_box");
    if (chat_scroll_box.scrollTop + chat_scroll_box.clientHeight == chat_scroll_box.scrollHeight) {
        autoscroll_active = true;
        // $('scrollmode_monitor').innerHTML = "true";
    } else {
        autoscroll_active = false;
        // $('scrollmode_monitor').innerHTML = (chat_scroll_box.scrollTop + chat_scroll_box.clientHeight - chat_scroll_box.scrollHeight);
    }
}

//--------------- chat update -----------------------


function chat_update()
{
    var min_key = (messages_sorted_max_key > messages_sorted_lookback_limit) ? messages_sorted_max_key : messages_sorted_lookback_limit;
    new Ajax.Request(baseuri + "json/ajaxchat/update/" + min_key, {
        method: "post",
        onComplete: chat_update_callback
    });
}

function chat_update_callback(transport)
{
    if (!transport.responseJSON) {
        alert('no responseJSON\n\n' + transport.responseText);
    } else {
        var json = transport.responseJSON;
        show_json_alerts(json.alerts);
        show_json_text(json.text);
        if (json.messages) {
            add_json_messages(json.messages);
            if (transport.transport.wait_element) {
                var wait_element = transport.transport.wait_element;
                wait_element.parentNode.removeChild(wait_element);
            }
            if (json.new_lookback_limit) {
                messages_sorted_max_key = json.new_lookback_limit;
            }
        }
    }
}

var messages_sorted = new Object();
var messages_sorted_max_key = '0';
var messages_sorted_lookback_limit = '<?=$lookback_limit ?>';

function add_json_messages(messages_json)
{
    if (!messages_json) return;

    // alert(messages_json.length + ' new messages fetched from server');
    for (var i=0; i<messages_json.length; ++i) {
        var message = messages_json[i];
        if (messages_sorted_lookback_limit < message.created && message.text) {
            // message.node = document.createElement('div');
            // message.node.innerHTML = innerHTML_for_message(message); 
            messages_sorted[message.created + '_' + message.id] = message;
        }
    }
    
    show_all_messages();
}

// do we really need this one?
function innerHTML_for_message(message) {
    return
        '<div style="margin:4px"><div style="color:#ddd">' + key + '<\/div><div>' +
        '<a href="bw/member.php?cid='+message.username+'">' + message.username + ':<\/a> ' +
        message.text + '<\/div>' + '<\/div>'
    ; 
}

function show_all_messages()
{
    var display = $('display');
    
    var accum_text = '';
    var username = false;
    
    for (var key in messages_sorted) {
        var message = messages_sorted[key];
        if (message.username != username) {
            username = message.username;
            // accum_text += '<div style="background:#aaccff;">' + username + '</div>';
            accum_text += '<hr style="border-color:#eee;"/>';
        }
        
        accum_text += 
            '<div style="margin:4px">' +
            '<div style="color:#ddd">' + key + '<\/div>' +
            '<div>' +
            '<a href="bw/member.php?cid=' + username + '">' + username + ':<\/a> ' +
            message.text + '<\/div><\/div>'
        ;
    }
    
    
    
    display.innerHTML = accum_text;
    
    scroll_down();
}


function show_json_alerts(alerts)
{
    if (alerts) {
        for (var i=0; i<alerts.length; ++i) {
            alert(alerts[i]);
        }
    }    
}

function show_json_text(text)
{
    // do nothing with the text..
}


//--------------- send message -----------------------

function chat_textarea_keyup(e) {
    keycode = key(e);
    if (13 == keycode) {
        send_chat_message();
    } else {
        // $("keycode_monitor").innerHTML = keycode;
    }
}

function key(e) {
    if (window.event) return window.event.keyCode;
    else if (e) return e.which;
    else return false;
}

function send_chat_message() {
    var params = $("ajaxchat_form").serialize(true);
    var wait_element = document.createElement("div");
    wait_element.innerHTML = "<?=$_SESSION['Username'] ?>: "+$('chat_textarea').value; 
    document.getElementById("waiting_send").appendChild(wait_element);
    document.getElementById("chat_textarea").value = "";
    var request = new Ajax.Request(baseuri + "json/ajaxchat/send", {
        method: "post",
        parameters: params,
        onComplete: chat_update_callback
    });
    request.transport.wait_element = wait_element;
    autoscroll_active = true;
    scroll_down();
}


//--------------------------------------------------------


//--></script>



<p><span id="update_button" class="button">update</span></p>


<div style="overflow:auto; border:1px solid grey; height:20em; width:40em;" id="chat_scroll_box" onscroll="on_manual_scroll()">
<div id="display"></div>
<div style="color:#666" id="waiting_update"></div>
<div style="color:#aaa" id="waiting_send"></div>
</div>
<br>
<!-- <div><span id="keycode_monitor"></span>, <span id="scrollmode_monitor"></span></div> -->
<br>
<form id="ajaxchat_form" method="POST" action="ajaxchat">
<textarea id="chat_textarea" name="chat_message_text" rows="7" cols="60"></textarea>
</form>

<div id="ajax_monitor">..</div>

<script type="text/javascript">

document.getElementById("update_button").onclick = chat_update;
document.getElementById("chat_textarea").onkeyup = chat_textarea_keyup;
chat_update();
setInterval(chat_update, 4500);






/*
Array.prototype.print_r = function()
{ 
    document.write( this.getDebugString() );
}
 
Array.prototype.alert_r = function()
{
    alert( this.getDebugString() );
}
 
Array.prototype.getDebugString = function( numTabs )
{
    // We subract one from numTabs here and add two later because of an odd illegal radix error
    numTabs  = ( typeof numTabs == 'undefined' ) ? 0 : numTabs - 1;
    var tabs    = ('\t').replicate( numTabs );
    var output = 'Array\n' + tabs + '(\n';
    for ( var i = 0, l = this.length; i < l; i++ )
    {
        output += tabs + '\t[' + i + '] => ' + this[i].getDebugString( numTabs + 2 ) + '\n';
    }
    return output + tabs + ')';
}
 
Object.prototype.getDebugString = function( numTabs )
{
    // We subract one from numTabs here and add two later because of an odd illegal radix error
    numTabs  = ( typeof numTabs == 'undefined' ) ? 0 : numTabs - 1;
    var tabs    = ('\t').replicate( numTabs );
    var output = 'Object\n' + tabs + '{\n';
    for ( var i in this )
    {
        output += tabs + '\t[' + i + '] => ' + this[i].getDebugString( numTabs + 2 ) + '\n';
    }
    return output + tabs + '}';
}
 
Function.prototype.getDebugString = function()
{
    return 'function() { ... }';
}
 
String.prototype.getDebugString  = String.prototype.toString;
Number.prototype.getDebugString  = Number.prototype.toString;
Boolean.prototype.getDebugString    = Boolean.prototype.toString;
 
// helper method
String.prototype.replicate = function( qty )
{
    var output = '';
    for ( var i = 0; i < qty; i++ )
    {
        output += this;
    }
    return output;
}


*/



</script>
