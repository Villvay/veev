
var fs = require('fs');
var http = require('http');
var mysql = require('mysql');

//	-----------------------------------------------------------------------------
//	Load configurations from php framework
//	-----------------------------------------------------------------------------
var config = fs.readFileSync(__dirname+'/config.php', 'UTF-8').split('\n');
var tmp = [];
//	This is parsed only once on start-up, So let's keep config in php.
for (var i = 0; i < config.length; i++)
	tmp.push(config[i].split('\''));
config = {};
for (var i = 0; i < tmp.length; i++){
	if (tmp[i].length == 5)
		config[tmp[i][1]] = tmp[i][3];
}

//	-----------------------------------------------------------------------------
//	Database - Keep a persistant connection to the database (reconnect after an error or disconnect)
//	-----------------------------------------------------------------------------
var databaseConnection = {retryMinTimeout: 2000, retryMaxTimeout: 60000};
var database, retryTimeout = 2000;
function persistantConnection(){
	try{
		database.end();
	}catch(e){}
	//
	database = mysql.createConnection({"host": config.DB_HOST, "database": config.DB_NAME, "user": config.DB_USER, "password": config.DB_PASS});
	database.connect(
		function (err){
			if (err){
				console.error('Error connecting to database: '+err.code);
				setTimeout(persistantConnection, retryTimeout);
				console.log('Retrying in '+(retryTimeout / 1000)+' seconds');
				if (retryTimeout < databaseConnection.retryMaxTimeout)
					retryTimeout += 1000;
			}
			else{
				retryTimeout = databaseConnection.retryMinTimeout;
				console.log('Connected to database');
			}
		});
	database.on('error',
		function (err){
			console.error('Database error: '+err.code);
			console.error(err);
			persistantConnection();
		});
}
persistantConnection();

//	Listen to port for requests from php application
var server = http.createServer(
	function (request, response){
		var url = request.url.split('/');
		if (url[0] == '')
			url.shift();
		//
		handlePost(request,
			function(data){
				if (url.length > 1){
					if (url[0] == 'start'){
						var key = (new Date()).getTime()+parseInt(Math.random()*100);
						response.end(JSON.stringify({'status': 'queued', 'jobId': key}));
						//
						QStat[key] = new stat(key);
						var module = require('../modules/'+url[1]+'/'+url[2]+'.js');
						module.run(database, QStat[key], url.splice(3));
					}
					//
					//	Status check
					else if (url[0] == 'status'){
						//jobProgressLongpollHndlr(url[2], response);
						if (typeof QStat[ url[1] ] == 'undefined')
							response.end(JSON.stringify({'error': 'invalid-key'}));
						else
							response.end( JSON.stringify({
								'completed': QStat[ url[1] ].completed,
								'error': QStat[ url[1] ].error,
								'progress': QStat[ url[1] ].progress,
								'result': QStat[ url[1] ].result,
								'started': QStat[ url[1] ].started,
								'total': QStat[ url[1] ].total
							}) );
					}
					else
						response.end(JSON.stringify({'status': 'service is running'}));
				}
				else
					response.end(JSON.stringify({'status': 'service is running'}));
				//response.end(JSON.stringify({'status': 'nothing to see here'}));
				//response.end(JSON.stringify(Object.keys(require.cache)));
			}
		);
	}
);

server.on('error',
	function(e){
		console.log('--------------------------------');
		console.log('\033[91m'+e.errno+'\033[0m');
		console.log({'EADDRINUSE': 'Port '+process.argv[2]+' is already in use.', 'EACCES': 'Port '+process.argv[2]+' is not allowed to bind.'}[e.errno]);
	});

server.listen(process.argv[2]);
console.log('Back-end service is ready');

//	-----------------------------------------------------------------------------
//	Handler for multipart POST request/response body
//	We are overloading this function to express app
//	-----------------------------------------------------------------------------
handlePost = function(req, callback, maxAllowed){
	if (!isset(maxAllowed))
		maxAllowed = 1e6;
	var body = '';
	req.on('data', function (data){
		body += data;
		//	Do not receive data more than 1 mB
		if (body.length > maxAllowed)
			req.connection.destroy();
	});
	req.on('end', function (data){
		var post = body;
		//	First try to parse as JSON - We should rather be looking into request header for this
		try{
			post = JSON.parse(post);
		}
		catch(e){
			try{
				//	Thn try to parse as x-form-url-encoded if JSON didn't work
				post = qs.parse(post);
			}
			catch(e){}
		}
		callback(post);
	});
}

var QStat = {};
//	Job status object to pass job progress to middleware and front-end
function stat(key, callback){
	var _this = this;
	this.started = new Date();
	this.progress = 0;
	this.total = 0;
	this.result = false; 		//	Output of the process
	this.error = []; 		//	Any errors encountered during the job
	this.completed = false; 	//	Weather this job is completed
	var distroyTimeout = false;
	//
	this.hitStep = function(){
		_this.progress += 1;
		clearTimeout(distroyTimeout);
		if (_this.progress == _this.total){
			if (typeof callback == 'function')
				callback(_this);
			if (typeof key != 'undefined'){
				_this.completed = true;
				distroyTimeout = setTimeout(
					function(){
						delete QStat[key];
					}, 10000);
			}
			console.log(key+': Completed.!');
			if (_this.error.length > 0)
				console.log(_this.error);
		}
	};
}

//	-----------------------------------------------------------------------------
//	Shorthand function to check if an object exists
//	-----------------------------------------------------------------------------
function isset(obj){
	return typeof obj != 'undefined';
}
