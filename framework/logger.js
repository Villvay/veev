//	This script logs any standard output
//	from the back-end service

var fs = require('fs');
var crypto = require('crypto');

process.stdin.resume();
process.stdin.setEncoding('utf8');

process.stdin.on('data', function(chunk){
	var data = {};
	try{
		data = JSON.parse(chunk);
		var hash = crypto.createHash('md5').update(chunk).digest('hex');
		fs.appendFile('../access.log', (new Date()).sqlFormatted()+'\t'+hash+'\t'+data.code+'\t'+data.query+'\n'+
			(data.code == 200 ? '' : data.code+': '+data.error+'\n'+data.file+' ['+data.line+']'),
			function (err){});
	}
	catch(e){
		fs.appendFile('../access.log', (new Date()).sqlFormatted()+'\t'+chunk+'\n',
			function (err){});
	}
});

//	-----------------------------------------------------------------------------
//	Format a Date object to SQL compatible string
//	-----------------------------------------------------------------------------
Date.prototype.sqlFormatted = function() {
	var yyyy = this.getFullYear().toString();
	var mm = (this.getMonth()+1).toString();
	var dd  = this.getDate().toString();
	var hrs = this.getHours();
	var mts = this.getMinutes();
	var sec = this.getSeconds();
	return yyyy +'-'+ (mm[1]?mm:'0'+mm) +'-'+ (dd[1]?dd:'0'+dd)+' '+(hrs[1]?hrs:'0'+hrs)+':'+(mts[1]?mts:'0'+mts)+':'+(sec[1]?sec:'0'+sec);
};
