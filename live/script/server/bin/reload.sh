echo "loading..."
pid=`pidof liveMaster`
echo $pid
kill -USR1 $pid
echo "loading success"
