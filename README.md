# linux_temperature_web_monitor

This is a simple monitor for temperature, fans, cpu and gpu usage; made for the powerful linux

Simply extract the folder in your web server, run it and follow the instructions given when browsing with your web browser

<img src="http://i.imgur.com/pb8qTVM.png">

This is currently made for NVIDIA drivers and lm_sensors
And it's using <a href="https://d3js.org/">d3js</a> to create the charts, and is made with both JS and PHP.
Data is stored in a SQL server.


(If you want better accuracy, change the sql types from INT to FLOAT)
The reason i chose INT is because it makes the row sizes about 1/5 the size.

Alternatives to this is <a href="http://www.monitorix.org/">monitorix</a> and <a href="http://www.zabbix.com/">zabbix</a>.
