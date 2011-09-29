YUI().use('io-base', 'node', function(Y) {

function complete(id, o, args) {
    var div = Y.one('#results');
    div.set("innerHTML", M.str.pmplugins_results_engine.done)
}

M.results_engine = {
    process: function(properties) {
        var uri = properties.source +"?id="+properties.pmclass;
        var request = Y.io(uri);
    }
}

Y.on('io:complete', complete, Y, 'process');

});