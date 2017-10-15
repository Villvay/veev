
var http = require('http');

//	Listen to port for requests from php application
var server = http.createServer(
	function (request, response){
		var url = request.url.split('/');
		if (url[0] == '')
			url.shift();
		//
		/*handlePost(request,
			function(data){
				response.end(JSON.stringify({'status': 'nothing to see here'}));
			}
		);*/
		response.end(JSON.stringify({'status': 'nothing to see here'}));
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

//	-----------------------------------------------------------------------------
//	Shorthand function to check if an object exists
//	-----------------------------------------------------------------------------
function isset(obj){
	return typeof obj != 'undefined';
}
