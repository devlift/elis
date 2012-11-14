/**
 * ELIS(TM): Enterprise Learning Intelligence Suite
 * Copyright (C) 2008-2012 Remote Learner.net Inc http://www.remote-learner.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    elis
 * @subpackage core
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2008-2012 Remote Learner.net Inc http://www.remote-learner.net
 */

/// Globals

// whether or not the scripts from the innerhtml have already been run
var innerhtml_scripts_run = false;

var lastrequest = basepage;

var selection = new Array();

var selection_field = null;

var set_content_callback = {
    success: set_content
};

YAHOO.util.Event.onDOMReady(function() {
    make_links_internal();
    window.selection_field = get_element_by_name("_selection");
    var set_checkboxes_callback = {
        success: set_checkboxes_success
    };
    YAHOO.util.Connect.asyncRequest("GET", window.basepage + '&mode=bare&action=get_checkbox_selection', set_checkboxes_callback);
});

var onbeforeunload = function(e) {
    update_checkbox_selection();
}

YAHOO.util.Event.addListener(document, 'unload', onbeforeunload);

/**
 * Returns the first element found that has the given name attribute
 */
function get_element_by_name(name) {
    return YAHOO.util.Dom.getElementsBy(function(el) { return el.getAttribute("name") == name; })[0];
}

function update_checkbox_selection() {
    var selectedcheckboxes = YAHOO.lang.JSON.stringify(window.selection);
    // Send the selected checkboxes synchronously
    YUI().use("io-base", function(Y) {
        var uri = basepage + "&action=checkbox_selection_session";
        var cfg = {
            method: 'POST',
            sync: true,
            data: 'selected_checkboxes=' + selectedcheckboxes
        };
        var request = Y.io(uri, cfg);
    });
}

/**
 * Convert all links and forms within the list_display div to load within the
 * div.
 */
function make_links_internal() {
    var list_display = document.getElementById('list_display');
    // catch any click events, to catch user clicking on a link
    YAHOO.util.Event.addListener(list_display, "click", load_link);
    // catch any form submit events
    // IE doesn't bubble submit events, so we have to listen on each form
    // element (which hopefully isn't too many)
    YAHOO.util.Dom.getElementsBy(function(el) { return true; }, 'form', 'list_display', function(el) {
        YAHOO.util.Event.addListener(el, "submit", load_form, el.getAttribute('id'));
    });
}

/**
 * When we receive new content from the server, replace the list_display div
 * with it.
 */
function set_content(resp) {
    var div = document.createElement('div');
    div.id = 'list_display';
    innerhtml_scripts_run = false;
    div.innerHTML = '<script>innerhtml_scripts_run = true;</script>' + resp.responseText;
    var olddiv = document.getElementById('list_display');
    olddiv.parentNode.replaceChild(div, olddiv);
    make_links_internal();
    mark_selected();
    if (!innerhtml_scripts_run) {
        YAHOO.util.Dom.getElementsBy(function(el) { return true; }, 'script', div.id, function(el) {
            eval(el.text);
        });
    }
}

/**
 * event handler for links within the list_display div
 */
function load_link(ev) {
    var target = YAHOO.util.Event.getTarget(ev);
    if (!target.getAttribute("href")) return;
    window.lastrequest = target.getAttribute("href");
    var selectedcheckboxes = YAHOO.lang.JSON.stringify(window.selection);
    YAHOO.util.Connect.asyncRequest("POST", window.lastrequest + "&mode=bare", set_content_callback, "selected_checkboxes=" + selectedcheckboxes);
    YAHOO.util.Event.preventDefault(ev);
}

/**
 * event handler for forms within the list_display div
 */
function load_form(ev) {
    var target = YAHOO.util.Event.getTarget(ev);
    var data = YAHOO.util.Connect.setForm(target);
    var link = target.getAttribute('action');
    window.lastrequest = link + '?' + data;
    var selectedcheckboxes = YAHOO.lang.JSON.stringify(window.selection);
    YAHOO.util.Connect.asyncRequest("POST", link + "?mode=bare", set_content_callback, "selected_checkboxes=" + selectedcheckboxes);
    YAHOO.util.Event.preventDefault(ev);
}

/**
 * event handler for "show selected only" checkbox
 */
function change_selected_display() {
    var selected_only = get_element_by_name("selectedonly");
    if (selected_only.checked) {
        if (window.selection != null) {
            var data = '[' + window.selection.join(',') + ']';
            if (!data) {
                data = "[]";
            }
        YAHOO.util.Connect.asyncRequest("POST", basepage + "&mode=bare&_showselection="+data, set_content_callback, "selected_checkboxes=" + data);
        }
    } else {
        var selectedcheckboxes = YAHOO.lang.JSON.stringify(window.selection);
        YAHOO.util.Connect.asyncRequest("POST", basepage + "&mode=bare", set_content_callback, "selected_checkboxes=" + selectedcheckboxes);
    }
}

function set_selected_checkboxes(sessionselection) {
    var checkedselection = sessionselection.split(',');
    for (var i = 0; i < checkedselection.length; i++) {
        if (checkedselection[i]) {
            var cb = get_element_by_name("select"+ checkedselection[i]);
            if (cb) {
                cb.checked = true;
            }
            window.selection.push(checkedselection[i]);
        }
    }
    window.selection_field = get_element_by_name("_selection");
    window.selection_field.value = '[' + window.selection.join(',') + ']';
    mark_selected();
}

function set_checkboxes_success(resp) {
    //alert('set_checkboxes_success: '+ resp.responseText);
    var selectedelem = document.getElementById('selected_checkboxes');
    if (selectedelem) {
        selectedelem.value = resp.responseText;
    }
    set_selected_checkboxes(resp.responseText);
}

function select_item(id) {
    if (get_element_by_name("select"+id).checked) {
        // Add checkbox selection
        if (checkbox_selection_index(id) == -1) {
            window.selection.push(id);
        }
    } else {
        // Remove checkbox selection
        var pos = checkbox_selection_index(id);
        if (pos != -1) {
            window.selection.splice(pos, 1);
        }
    }
    window.selection_field.value = '[' + window.selection.join(',') + ']';
    document.getElementById("numselected").innerHTML = selection.length;
}

function checkbox_selection_index(element) {
    for (var i = 0; i < window.selection.length; i++) {
        if (window.selection[i] == element) {
            return i;
        }
    }
    return -1;
}

/**
 * when the table is loaded, mark which elements have already been selected
 */
function mark_selected() {
    var table = document.getElementById('selectiontable');
    var numselected = 0;
    if (table) {
        YAHOO.util.Dom.getElementsBy(function(el) { return true; }, 'input', table, function(el) {
            var id = el.name.substr(6);
            if (checkbox_selection_index(id) == -1) {
                el.checked = false;
            } else {
                el.checked = true;
            }
            if (el.checked) numselected++;
        });
    }
    var sessionselection = document.getElementById('selected_checkboxes');
    var length = 0;
    if (sessionselection && sessionselection.value) {
        length = sessionselection.value.split(',').length;
        //alert('checkbox_selection.js::mark_selected(): length = '+ length);
    }

    document.getElementById("numonotherpages").innerHTML = (length - numselected);
    document.getElementById("numselected").innerHTML = length;

    if (length != numselected) {
        document.getElementById("selectedonotherpages").style.display = 'inline';
    } else {
        document.getElementById("selectedonotherpages").style.display = 'none';
    }
}

function checkbox_select(checked) {
    var table = document.getElementById('selectiontable');
    if (table) {
        YAHOO.util.Dom.getElementsBy(function(el) { return true; }, 'input', table, function(el) {
            el.checked = checked;
            id = el.name.substr(6);
            select_item(id);
        });
    }
}

