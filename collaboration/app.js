var connect = require('connect'),
    sharejs = require('share').server;

var server = connect(
      connect.logger(),
      connect.static(__dirname + '/public'));

var isValidAgent = function(auth) {
	return auth.hash === auth.userId + auth.projectId;
}

var options = {
	db: {type: 'none'},
	browserChannel: { cors: "*" },  // TODO
	auth: function(agent, action) {
		if (action.type === 'connect' && !isValidAgent(agent.authentication))
			action.reject();
		else
			action.accept();
	}
};

// Attach the sharejs REST and Socket.io interfaces to the server
sharejs.attach(server, options);

server.listen(8000, function(){
    console.log('Server running at http://127.0.0.1:8000/');
});
