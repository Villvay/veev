//	This script logs any standard output
//	from the back-end service

var fs = require('fs');

process.stdin.resume();
process.stdin.setEncoding('utf8');

process.stdin.on('data', function(chunk) {
	fs.appendFile('../access.log', '['+(new Date())+'] '+chunk,
	function (err){});
});
