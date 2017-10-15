#	This script starts the backend service

nohup node service.js $1 | node logger.js 2>&1 &
echo $! > ../data/service.pid
