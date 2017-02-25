# linux_temperature_web_monitor

This is a simple monitor for temperature, fans, cpu and gpu usage; made for the powerful linux

Simply extract the folder in your web server, run it and follow the instructions given when browsing with your web browser

<img src="http://i.imgur.com/pb8qTVM.png">

This is currently made for NVIDIA drivers and lm_sensors
Alternatives to this is monitorix and zabbix.

(If you want better accuracy, change the sql types from INT to FLOAT)
The reason i choose INT is because it makes the row sizes about 1/5 the size.
