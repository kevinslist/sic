(function($){
$.extend({
	websocketSettings: {
		open: function(){},
		close: function(){},
		message: function(){},
		options: {},
		events: {}
	},
	websocket: function(url, s) {
		var ws = WebSocket ? new WebSocket( url ) : {
			send: function(m){ return false },
			close: function(){}
		};
		$(ws)
			.bind('onopen', $.websocketSettings.open)
			.bind('onclose', $.websocketSettings.close)
			.bind('onmessage', $.websocketSettings.message)

		$(window).unload(function(){ ws.close(); ws = null });
		return ws;
	}
});
})(jQuery);
