<?php
/*
License:
Creative Commons Attribution-ShareAlike 4.0 International
License owner: Kristoffer Bernssen
Initial Development Release 24/2-17
*/

require('config.inc.php');
echo'<!DOCTYPE html><html><head>
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
</head><body onunload="">';
$db = new PDO('mysql:host='.$database_host.';dbname='.$database.';charset=utf8', ''.$username.'', ''.$password.'');
#$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
$output=`nvidia-smi --query-gpu=temperature.gpu --format=csv,noheader`;
$nv=str_replace("\n","",$output);


$toexpect=[
           'temp1'=>'temp1',
           'temp2'=>'temp2',
'temp3'=>'temp3',
'temp4'=>'temp4',
'Physical id 0'=>'phy0',
'Core 0'=>'core0',
'Core 1'=>'core1',
'Core 2'=>'core2',
'Core 3'=>'core3',
'Core 4'=>'core4',
'Core 5'=>'core5',
'Core 6'=>'core6',
'Core 7'=>'core7',
'cpu_fan'=>'cpufan'];
$whatfound=[];
#temp1,temp2,physical id0,core0,core1,core2,core3,nvidia
$r = $db->prepare('INSERT INTO `pctemps`
                  (nvidia,temp1,temp2,phy0,core0,core1,core2,core3,core4,core5,core6,core7,cput,gpuu,gpufanp,gpumem,cpufan) VALUES
                  (:nvidia,:temp1,:temp2,:phy0,:core0,:core1,:core2,:core3,:core4,:core5,:core6,:core7,:cput,:gpuu,:gpufanp,:gpumem,:cpufan);');
$output=`sensors | grep crit`;
$output_e=explode("\n",$output);
foreach($output_e as $op){
    if(strstr($op,'crit =')){
        preg_match('/^([^:]*)[^\+]*\+(\S*)\sC/',$op,$ms);
        if($toexpect[$ms[1]]){
            $r->bindValue(':'.$toexpect[$ms[1]],$ms[2],PDO::PARAM_STR);
            $whatfound[$ms[1]]=1;
        }
    }
}
foreach($toexpect as $a=>$b){
    if(!$whatfound[$a]){
        $r->bindValue(':'.$b,0,PDO::PARAM_INT);
    }
}
if(empty($nv)){$nv=0;}
$r->bindValue(':nvidia',$nv,PDO::PARAM_STR);
$output=`nvidia-smi -i 0 -q | grep % | grep Gpu | cut -d ':' -f 2 | cut -d ' ' -f 2`;
$gpuu=str_replace("\n","",$output);
$r->bindValue(':gpuu',$gpuu,PDO::PARAM_STR);
$output=`nvidia-smi -i 0 -q | grep % | grep Fan | cut -d ':' -f 2 | cut -d ' ' -f 2`;
$gpufanp=str_replace("\n","",$output);
$r->bindValue(':gpufanp',$gpufanp,PDO::PARAM_STR);
$output=`nvidia-smi -i 0 -q | grep % | grep Memory | cut -d ':' -f 2 | cut -d ' ' -f 2`;
$gpumem=str_replace("\n","",$output);
$r->bindValue(':gpumem',$gpumem,PDO::PARAM_STR);

#$output=`grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'`;
#$output=`grep -oP '(?<=cpu cores\t:\s)(\d{1,3})' /proc/cpuinfo | head -1`;
$output=`nproc`;
$numcores=str_replace("\n","",$output);
$output=`uptime | awk '{ print $11 }' | cut -d ',' -f 1,2`;

$cput=str_replace("\n","",$output);
$cput=($cput*100)/$numcores;
$r->bindValue(':cput',$cput,PDO::PARAM_STR);


try {$r->execute();} catch(PDOException $ex) {echo $ex->getMessage();}

$r2_p = $db->prepare('select * from pctemps order by ts desc limit 0,10');
$r2_p->execute();
$num_r2_p=$r2_p->rowCount();
$r2_v=$r2_p->fetchAll();
#
$i=0;foreach($r2_v as $r){
    if($i==0){
        if(empty($r['core4'])){
            $no8core=1;
            $fileputty="x,gpu\t\t\t[C],temp1\t\t\t[C],temp2\t\t\t[C],phy0\t\t\t[C],core0\t\t\t[C],core1\t\t\t[C],core2\t\t\t[C],core3\t\t\t[C],cpu usage\t\t\t[%],gpu usage\t\t\t[%],gpu fan\t\t\t[%],gpu memory usage\t\t\t[%],cpu fan\t\t\t[rpm]\n";
        }else{
            $fileputty="x,gpu\t\t\t[C],temp1\t\t\t[C],temp2\t\t\t[C],phy0\t\t\t[C],core0\t\t\t[C],core1\t\t\t[C],core2\t\t\t[C],core3\t\t\t[C],core4\t\t\t[C],core5\t\t\t[C],core6\t\t\t[C],core7\t\t\t[C],cpu usage\t\t\t[%],gpu usage\t\t\t[%],gpu fan\t\t\t[%],gpu memory usage\t\t\t[%],cpu fan\t\t\t[rpm]\n";
        }
    }
    if(isset($no8core)){
       $all=$r['ts'].','.$r['nvidia'].','.$r['temp1'].','.$r['temp2'].','.$r['phy0'].','.$r['core0'].','.$r['core1'].','.$r['core2'].','.$r['core3'].','.
       $r['cput'].','.$r['gpuu'].','.$r['gpufanp'].','.$r['gpumem'].','.$r['cpufan'];
    }else{
       $all=$r['ts'].','.$r['nvidia'].','.$r['temp1'].','.$r['temp2'].','.$r['phy0'].','.$r['core0'].','.$r['core1'].','.$r['core2'].','.$r['core3'].','.
       $r['core4'].','.$r['core5'].','.$r['core6'].','.$r['core7'].','.$r['cput'].','.$r['gpuu'].','.$r['gpufanp'].','.$r['gpumem'].','.$r['cpufan']; 
    }
    $fileputty.=$all."\n";
    ++$i;
}
    
file_put_contents('temps.cvs',$fileputty);
echo'</body></html>';
?>