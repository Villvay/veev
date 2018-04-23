
//	This is an example long running background process.

module.exports = {

	//	Implements the background process
	//	database: We get a connection to the database,
	//	cache shared with main php web-application
	//	stat object to report back progress,
	//	array of parameters if any
	run : function(database, cache, stat, params){
		//	Target steps
		stat.total = 20;
		// This can be changed later on, as we discover new steps to process
		//
		var add = function(){
			//	Notify back completion of a single step
			stat.hitStep();
			// When hits equal total, process is considered completed
			//
			//	Let's say we discover one more step per each two steps completed
			stat.total += 1;
			stat.hitStep();
			//
			//	Continue processing - for demonstration
			if (stat.progress < stat.total)
				setTimeout(arguments.callee, 1000);
			else
				//	Update result
				stat.result = stat.progress;
		};
		add();
	}

};
