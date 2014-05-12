var config = require('./config.json');

if ( config.seed === "1" ) {
    console.log("Warning: Seed not set");
}

var connect = require('connect'),
    sharejs = require('share').server;

var server = connect(
      connect.logger(),
      connect.static(__dirname + '/public'));

var isValidAgent = function(auth) {
    var crypto = require('crypto')
    , shasum = crypto.createHash('sha256');
    shasum.update(config.seed + auth.projectId);
	  return auth.hash === shasum.digest('hex');
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
