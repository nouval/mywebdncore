var FxTracker = FxTracker || {};

if (!FxTracker.funcs) {
    FxTracker.funcs = {
        version: function () {
            return "0.1";
        },
        hookMe: function () {
            var
                // sending event id to fxtracker api
                postLog = function (eid) {
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onload = function (e) {
                        // do something when response is received
                    };
                    xmlhttp.open('POST', '/api/fxtracker/log', true);
                    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xmlhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
                    xmlhttp.responseType = 'json';
                    xmlhttp.send(JSON.stringify({ eventId: eid }));
                },
                // loop through document and find elements with 'fte-' tag 
                findfte = function () {
                    var el, attr, i, j, arr = [],
                        reg = new RegExp('^fte-', 'i'),
                        els = document.getElementsByTagName('*');

                    for (i = 0; i < els.length; i++) {                 //loop through all tags
                        el = els[i]                                    //our current element
                        attr = el.attributes;                          //its attributes
                        dance: for (j = 0; j < attr.length; j++) {     //loop through all attributes
                            if (reg.test(attr[j].name)) {              //if an attribute starts with mce_
                                arr.push(el);                          //push to collection
                                break dance;                           //break this loop
                            }
                        }
                    }
                    return arr;
                },
                // global eventid for page load, there can be only one
                eidpl = null,
                // binds document to specific event (currently support only 'click' event)
                bindFte = function () {
                    items = findfte();
                    // loop through all fxtracker tags
                    for (var idx in items) {
                        if (eid = (items[idx].getAttribute('fte-cl') || null)) {
                            items[idx].addEventListener('click', function (e) {
                                postLog(e.target.getAttribute('fte-cl'));
                            });
                        } else if (eid = (items[idx].getAttribute('fte-sl') || null)) {
                            items[idx].addEventListener('change', function(e) {
                                postLog(e.target.getAttribute('fte-sl'));
                            }, false);
                        } else if (eid = (items[idx].getAttribute('fte-kp-ent') || null)) {     // keypress 'enter'
                            items[idx].addEventListener('keypress', function(e) {
                                if ((e.which || e.keyCode) == 13) {
                                    postLog(e.target.getAttribute('fte-kp-ent'));
                                }
                            }, false);
                        } else if (eid = (items[idx].getAttribute('fte-dl') || null)) {
                            items[idx].addEventListener('DOMSubtreeModified', function(e) {
                                postLog(e.target.getAttribute('fte-dl'));
                            }, false);                            
                        } else if (eid = (items[idx].getAttribute('fte-pl') || null)) {
                            eidpl = eid;
                        }
                    }
                };
            // hook me to windows.onload
            window.onload = function (e) {
                // bind fxtracker events
                bindFte();
                if (eidpl) {
                    postLog(eidpl);
                }
            }
        }
    };
}

FxTracker.funcs.hookMe();