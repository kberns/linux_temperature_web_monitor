<?php
echo'
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="c3.css">
  </head>
  <body>';
if(isset($_GET['dbi'])){
  $dbp=$_POST['password'];
  $dbu=$_POST['username'];
  $dbdb=$_POST['database'];
  $dbh=$_POST['hostname'];
  $db = new PDO('mysql:host='.$dbh.';charset=utf8', ''.$dbu.'', ''.$dbp.'');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  
  try {
  $r = $db->query('CREATE DATABASE IF NOT EXISTS '.addslashes($dbdb).';');
  } catch(PDOException $ex) {echo $ex->getMessage();}
 
  try {
   $r = $db->query('USE '.addslashes($dbdb).';');
  } catch(PDOException $ex) {echo $ex->getMessage();}
  
  $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,false);
   try {$r = $db->query('CREATE TABLE IF NOT EXISTS '.addslashes($dbdb).'.pctemps (
    ti int(10) unsigned AUTO_INCREMENT,
    PRIMARY KEY(ti),
    ts timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    temp1 int(1) unsigned,
    temp2 int(1) unsigned,
    phy0 int(1) unsigned,
    core0 int(1) unsigned,
    core1 int(1) unsigned,
    core2 int(1) unsigned,
    core3 int(1) unsigned,
    core4 int(1) unsigned,
    core5 int(1) unsigned,
    core6 int(1) unsigned,
    core7 int(1) unsigned,
    nvidia int(1) unsigned,
    cput int(1) unsigned,
    cpu0 int(1) unsigned,
    cpu1 int(1) unsigned,
    cpu2 int(1) unsigned,
    cpu3 int(1) unsigned,
    cpu4 int(1) unsigned,
    cpu5 int(1) unsigned,
    cpu6 int(1) unsigned,
    cpu7 int(1) unsigned,
    cpufan int(2) unsigned,
    gpuu int(1) unsigned,
    gpufanp int(1) unsigned,
    gpumem int(1) unsigned
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHARACTER SET=utf8 COLLATE=utf8_general_ci;');} catch(PDOException $ex) {echo $ex->getMessage();}
  file_put_contents('config.inc.php',"<?php 
    \$database_host='$dbh';
    \$database='$dbdb';
    \$username='$dbu';
    \$password='$dbp';
   ?>");
  echo'<br><a href="?">Installation Complete<br>Click here to start monitoring</a>';
}
else{
  if(!is_file('config.inc.php')){
    echo'<form action="?dbi" method="POST">
    <b>Database installation.</b><br>
    Database hostname:<input type="text" name="hostname"><br>
    Database name:<input type="text" name="database"><br>
    Database username:<input type="text" name="username"><br>
    Database password:<input type="password" name="password"><br>
    <input type="submit" value="Install"></form>';
  }else{echo'<div id="chart"></div><script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>';}
}
echo'
    <script src="c3.js"></script>
    <script>
        var timeouttime=5000;
      var chart = c3.generate({
        data: {
             x: \'x\',
          columns: [
            [\'x\', \'2013-01-01\'],
            [\'gpu\t\t\t[C]\', 55]
          ],
          onclick: function (d, element) { console.log("onclick", d, element); },
          onmouseover: function (d) { console.log("onmouseover", d); },
          onmouseout: function (d) { console.log("onmouseout", d); },
        },
        axis: {
        x: {
            type: \'category\'
        }
    }
      });
  

        var ajaxRequest = new XMLHttpRequest();

       ajaxRequest.onreadystatechange=function() {
            //alert(ajaxRequest.readyState + " " + ajaxRequest.status);
            if (ajaxRequest.readyState==4 && ajaxRequest.status==200) {
               chart.load({
                    url: \'temps.cvs?\' + Date.now()
                });
            }
        }
      function lf(chart){
        ajaxRequest.open("GET", "temps.php?" + Date.now(), true);
        ajaxRequest.send(null);
        
      }
      setInterval(function () {
        lf(chart);
    }, timeouttime);
    </script>
   
  </body>
</html>
';#<a onclick="lf(chart)">LOADFILE</a>
?>