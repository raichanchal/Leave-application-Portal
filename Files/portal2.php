<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Teacher's leave login Portal</title>
<link rel='stylesheet' a href="style.css"/>
</head>
<body>
<?php
//include "portal.php";
$host = "localhost";
$user = "postgres";
$pass = "assassinwifi";
$db = "postgres";
$con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("Could not connect to server\n");
$name=$_GET['username'];
$email=$_GET['email'];
$pos=$_GET['pos'];
$indie=0;
$flag1=0;
if(isset($_GET['approve'])){
$id=$_GET['ID'];
}
if(isset($_GET['rej'])){
    $id=$_GET['ID'];
    }
pg_query("select auto_cancell();");
$query2="select * from faculty where faculty_id='$email';";
$result2=pg_query($query2);
$row=pg_fetch_row($result2);
echo "<p style='text-align:right;'><h4 style=\"color:white;\">Department :'$row[2]'</h4></p>";
if($row[3]<0){
    $flag1=1;$row[3]=0;}
echo "<p style='text-align:right;'><h4 style=\"color:white;\">Leaves Remaining '$row[3]'</h4></p>";
echo "<p style='text-align:right;'><h4 style=\"color:white;\">Year :'$row[4]'</h4></p>";
$sub=pg_query("select * from hod_leave where faculty_id='$email';");
$sub=pg_fetch_row($sub);
if(($sub)){
    echo "<center><h4 style=\"color:white;\">Application currently with the HOD</h4></center>";
    echo "<center><h4 style=\"color:white;\">Submitted on '$sub[9]'</h4></center>";
    $indie=1;
}
$sub=pg_query("select * from dean_leave where faculty_id='$email';");
$sub=pg_fetch_row($sub);
if(($sub)){
    echo "<center><h4 style=\"color:white;\">Application currently with the Dean</h4></center>";
    echo "<center><h4 style=\"color:white;\">Submitted on '$sub[11]'</h4></center>";
    echo "<center><h4 style=\"color:white;\">Comments made by HOD: '$sub[8]'</h4></center>";
    echo "<center><h4 style=\"color:white;\">Approved by HOD on '$sub[12]'</h4></center>";
    $indie=1;
}
$sub=pg_query("select * from dir_leave where faculty_id='$email';");
$sub=pg_fetch_row($sub);
if(($sub)){
    echo "<center><h4 style=\"color:white;\">Application currently with the Director</h4></center>";
    echo "<center><h4 style=\"color:white;\">Submitted on '$sub[9]'</h4></center>";
    if($sub[7]<$sub[9]){
    echo "<h4 style='color:white'><center>$sub[10]</h4></center>";
    }
    $indie=1;
}
//echo "<center><h3 style='color:white;'>Anything else $name</h3></center>";
//echo $row[1]
if(isset($_GET['approve'])){
                //Condition of row[1] also needed to check from where to delete and where to insert
    if($pos=='hod'){
        $sub=pg_query("select * from hod_leave where faculty_id='$id';");
        $sub=pg_fetch_row($sub);
        if($sub){
        if((strtotime($sub[8])-strtotime($sub[7]))/(60*60*24)<=$sub[4]){
        echo "<center><h3 style='color:white'>$id Approved</h3></center>";
        $comm=$_GET['comm'];
        pg_query("insert into dean_leave values('$sub[0]','$sub[1]','$sub[2]','$sub[3]','$sub[4]','$sub[5]','$sub[6]','$email','$comm','$sub[7]','$sub[8]','$sub[9]',now());");
        pg_query("delete from hod_leave where faculty_id='$id';");
        }
        else{
            echo "<center><h3 style='color:white'>$id Approved but salary might be deducted</h3></center>";
        $comm=$_GET['comm'];
        pg_query("insert into dean_leave values('$sub[0]','$sub[1]','$sub[2]','$sub[3]','$sub[4]','$sub[5]','$sub[6]','$email',concat('$comm',' .','Has applied for more number of leaves this year'),'$sub[7]','$sub[8]','$sub[9]',now());");
        pg_query("delete from hod_leave where faculty_id='$id';");
        }
        }
        else{
            echo "<center><h3 style='color:white'>Invalid approval</h3></center>";
        }
    }
    else if($pos=='dean'){
        $sub=pg_query("select * from dean_leave where faculty_id='$id';");
        $sub=pg_fetch_row($sub);
        if($sub){
        $comm=$_GET['comm'];
        if($sub[9]>=$sub[11]) {  //normal leave
        pg_query("update faculty set leave_left=leave_left-1-('$sub[10]'::date-'$sub[9]'::date) where faculty_id='$id';");
        $temp=pg_query("select leave_left from faculty where faculty_id='$id';");
        $temp=pg_fetch_row($temp);
        pg_query("delete from msg where faculty_id='$id';");
        if($temp[0]>=0)
        {
            pg_query("insert into archive values('$sub[0]','$sub[1]','$sub[2]','$sub[3]','$sub[5]','$sub[9]','$sub[10]',now(),'$sub[8]','$comm','N/A','$sub[11]');");
        }
        else{
            pg_query("insert into archive values('$sub[0]','$sub[1]','$sub[2]','$sub[3]','$sub[5]','$sub[9]','$sub[10]'::date+$temp[0],now(),'$sub[8]','$comm','N/A','$sub[11]');");
            
        }
    }
    else{  pg_query("insert into dir_leave values('$sub[0]','$sub[1]','$sub[2]','$sub[3]','$sub[4]','$sub[5]','$sub[6]','$sub[9]','$sub[10]','$sub[11]',concat('HOD comments: ','$sub[8]',' Dean comments:','$comm'));");  //retrospective leave

    }
        pg_query("delete from dean_leave where faculty_id='$id';"); 
        }
        else{
            echo "<center><h3 style='color:white'>Invalid approval</h3></center>";
        }
    }
    else{
        $sub=pg_query("select * from dir_leave where faculty_id='$id';");
        $sub=pg_fetch_row($sub);
        if($sub){
        $comm=$_GET['comm'];
        pg_query("update faculty set leave_left=leave_left-1-('$sub[8]'::date-'$sub[7]'::date) where faculty_id='$id';"); 
        $temp=pg_query("select leave_left from faculty where faculty_id='$id';");
        $temp=pg_fetch_row($temp);
        pg_query("delete from msg where faculty_id='$id';");
        if($sub[7]>=$sub[9]){
            if($temp[0]>=0){
        pg_query("insert into archive values('$sub[0]','$sub[1]','$sub[2]','$sub[3]','$sub[5]','$sub[7]','$sub[8]',now(),'N/A','N/A','$comm','$sub[9]');");
            }
            else{
                pg_query("insert into archive values('$sub[0]','$sub[1]','$sub[2]','$sub[3]','$sub[5]','$sub[7]','$sub[8]'::date+$temp[0],now(),'N/A','N/A','$comm','$sub[9]');");
    
            }
        }
        else{
            if($temp[0]>=0){
            pg_query("insert into archive values('$sub[0]','$sub[1]','$sub[2]','$sub[3]','$sub[5]','$sub[7]','$sub[8]',now(),'In the director section','In the director section',concat('$comm',' ','$sub[10]'),'$sub[9]');");
            }
            else{
                pg_query("insert into archive values('$sub[0]','$sub[1]','$sub[2]','$sub[3]','$sub[5]','$sub[7]','$sub[8]'::date+$temp[0],now(),'In the director section','In the director section',concat('$comm',' ','$sub[10]'),'$sub[9]');");
            
            }
        }
        pg_query("delete from dir_leave where faculty_id='$id';"); 
        }
        else{
            echo "<center><h3 style='color:white'>Invalid approval</h3></center>";
        }
    }
    }
elseif(isset($_GET['rej'])){
    if($pos=='hod'){
        $sub=pg_query("select * from hod_leave where faculty_id='$id';");
        $sub=pg_fetch_row($sub);
        if($sub){
        echo "<center><h3 style='color:white'>$id Rejected</h3></center>";
        $comm=$_GET['comm'];
        pg_query("insert into rejected values('$id','$row[2]','HOD','$comm',now());");
        pg_query("delete from hod_leave where faculty_id='$id';");
        pg_query("delete from msg where faculty_id='$id';");
        }
        else{
            echo "<center><h3 style='color:white'>Invalid rejection</h3></center>";
        }
    }
    elseif($pos=='dean'){
        $sub=pg_query("select * from dean_leave where faculty_id='$id';");
        $sub=pg_fetch_row($sub);
        if($sub){
        echo "<center><h3 style='color:white'>$id Rejected</h3></center>";
        $comm=$_GET['comm'];
        pg_query("insert into rejected values('$id','$row[2]','Dean','$comm',now());");
        pg_query("delete from dean_leave where faculty_id='$id';");
        pg_query("delete from msg where faculty_id='$id';");
        }
        else{
            echo "<center><h3 style='color:white'>Invalid rejection</h3></center>";
        }
    }
    else{
        $sub=pg_query("select * from dir_leave where faculty_id='$id';");
        $sub=pg_fetch_row($sub);
        if($sub){
        echo "<center><h3 style='color:white'>$id Rejected</h3></center>";
        $comm=$_GET['comm'];
        pg_query("insert into rejected values('$id','$row[2]','Director','$comm',now());");
        pg_query("delete from dir_leave where faculty_id='$id';");
        pg_query("delete from msg where faculty_id='$id';");
        }
        else{
            echo "<center><h3 style='color:white'>Invalid rejection</h3></center>";
        }
    }
}
elseif(isset($_GET['sub'])){
    $stat=$_GET['status'];
    $begin = $_GET['begin'];
    $end = $_GET['end'];
    if($row[3]<=0){
        echo "<center><h3 style='color:white'>You cannot apply for more leaves this year through the portal</h3></center>";
    }
    elseif($begin>$end){
        echo "<center><h3 style='color:white'>Please fill the application properly</h3></center>";
    }
    elseif($stat==0){
$sub=pg_query("select * from rejected where faculty_id='$email';");
    echo "<center><h3 style='color:white'>Leave application submitted</h3></center>";
    pg_query("insert into msg values('$email','','','','$row[2]');");
    $stat=1;
    //echo"<center><h3 style='color:white'>'$begin'</h3></center>";
    $app=$_GET['app'];
    $left=10-$row[3];
        if($pos=='emp'){
        pg_query("insert into hod_leave values ('$email','$name','$row[2]','$app','$row[3]','$row[4]','$left','$begin','$end',now());");
        }
        else{
            pg_query("insert into dir_leave values ('$email','$name','$row[2]','$app','$row[3]','$row[4]','$left','$begin','$end',now());");    
        }
    if($sub){
        pg_query("delete from rejected where faculty_id='$email';");
    }
    }
    else{
        echo "<center><h3 style='color:white'>One application has already been submitted</h3></center>";
    }
}
elseif(isset($_GET['h']) || isset($_GET['d'])){
$chID=$_GET['ch'];
$detes=pg_query("select * from faculty where faculty_id='$chID';");
$detes=pg_fetch_row($detes);
if($detes){
if(isset($_GET['h'])){
    $res=pg_query("select * from faculty where dept_name='$detes[2]' and position='hod';");
    $res=pg_fetch_row($res);
    $lev=10-$res[3];
    $find=pg_query("select * from dir_leave where faculty_id='$res[0]';");
    $find=pg_fetch_row($find);
    if($find){
        pg_query("delete from dir_leave where faculty_id='$res[0]';");
        pg_query("insert into hod_leave values ('$res[0]','$res[1]','$res[2]','$find[3]','$res[3]','$res[4]','$lev','$find[7]','$find[8]','$find[9]');");
        
    }
    $find=pg_query("select * from hod_leave where faculty_id='$detes[0]';");
    $find=pg_fetch_row($find);
    if($find){
        pg_query("delete from hod_leave where faculty_id='$detes[0]';");
        pg_query("insert into dir_leave values ('$find[0]','$find[1]','$find[2]','$find[3]','$find[4]','$find[5]','$find[6]','$find[7]','$find[8]','$find[9]','None');");
        
    }
    $find=pg_query("select * from dean_leave where faculty_id='$detes[0]';");
    $find=pg_fetch_row($find);
    if($find){
        pg_query("delete from dean_leave where faculty_id='$detes[0]';");
        pg_query("insert into dir_leave values ('$find[0]','$find[1]','$find[2]','$find[3]','$find[4]','$find[5]','$find[6]','$find[9]','$find[10]','$find[11]','$find[8]');");
        
    }
    pg_query("update faculty set position='$detes[5]' where dept_name='$detes[2]' and position='hod';");
    pg_query("update faculty set position='hod' where faculty_id='$detes[0]';");
}
else{
    $res=pg_query("select * from faculty where position='dean';");
    $res=pg_fetch_row($res);
    $lev=10-$res[3];
    $find=pg_query("select * from dir_leave where faculty_id='$res[0]';");
    $find=pg_fetch_row($find);
    if($find){
        pg_query("delete from dir_leave where faculty_id='$res[0]';");
        pg_query("insert into hod_leave values ('$res[0]','$res[1]','$res[2]','$find[3]','$res[3]','$res[4]','$lev','$find[7]','$find[8]','$find[9]');");
        
    }
    $find=pg_query("select * from hod_leave where faculty_id='$detes[0]';");
    $find=pg_fetch_row($find);
    if($find){
        pg_query("delete from hod_leave where faculty_id='$detes[0]';");
        pg_query("insert into dir_leave values ('$find[0]','$find[1]','$find[2]','$find[3]','$find[4]','$find[5]','$find[6]','$find[7]','$find[8]','$find[9]');");
        
    }
    $find=pg_query("select * from dean_leave where faculty_id='$detes[0]';");
    $find=pg_fetch_row($find);
    if($find){
        pg_query("delete from dean_leave where faculty_id='$detes[0]';");
        pg_query("insert into dir_leave values ('$find[0]','$find[1]','$find[2]','$find[3]','$find[4]','$find[5]','$find[6]','$find[9]','$find[10]','$find[11]');");
        
    }
    if($res[2]==$detes[2]){
    pg_query("update faculty set position='$detes[5]' where position='dean';");
    }
    else{
        pg_query("update faculty set position='emp' where position='dean';");
    }
    pg_query("update faculty set position='dean' where faculty_id='$detes[0]';");
}
}
else{
    echo "<h3 style='color:white;'><center>Please Assign a valid faculty to the position</h3></scenter><br>";
}
}
else if(isset($_GET['rr'])){
    echo "<h3 style='color:white;'><center>Response sent</h3></scenter><br>";
    $ans=$_GET['aa'];
    $subb=pg_query("select * from hod_leave where faculty_id='$email';");
    $subb=pg_fetch_row($subb);
    if(($subb)){
       pg_query("update msg set hod_comm=concat(hod_comm,' employee reply:','$ans') where faculty_id='$email';");
    }
    $subb=pg_query("select * from dean_leave where faculty_id='$email';");
    $subb=pg_fetch_row($subb);
    if(($subb)){
        pg_query("update msg set dean_comm=concat(dean_comm,' employee reply:','$ans') where faculty_id='$email';");
    }
    $subb=pg_query("select * from dir_leave where faculty_id='$email';");
    $subb=pg_fetch_row($subb);
    if(($subb)){
        pg_query("update msg set dir_comm=concat(dir_comm,' employee reply:','$ans') where faculty_id='$email';");
    }
}
elseif(isset($_GET['cc'])){
    $who=$_GET['ff'];
$comu=$_GET['ll'];
$temp2=pg_query("select * from msg where faculty_id='$who';");
$temp2=pg_fetch_row($temp2);
if($temp2){
    echo "<h3 style='color:white;'><center>Comments sent</h3></scenter><br>";
$subb=pg_query("select * from hod_leave where faculty_id='$who';");
$subb=pg_fetch_row($subb);
if(($subb)){
   pg_query("update msg set hod_comm=concat(hod_comm,' HOD comment:','$comu') where faculty_id='$who';");
}
$subb=pg_query("select * from dean_leave where faculty_id='$who';");
$subb=pg_fetch_row($subb);
if(($subb)){
    pg_query("update msg set dean_comm=concat(dean_comm,' Dean comment:','$comu') where faculty_id='$who';");
}
$subb=pg_query("select * from dir_leave where faculty_id='$who';");
$subb=pg_fetch_row($subb);
if(($subb)){
    pg_query("update msg set dir_comm=concat(dir_comm,' Director comment:','$comu') where faculty_id='$who';");
}
}
else{
    echo "<h3 style='color:white;'><center>Check again</h3></scenter><br>";
}
}
else if(isset($_GET['log'])){
    header('Location:login.php');
}

$sub=pg_query("select * from rejected where faculty_id='$email';");
$sub=pg_fetch_row($sub);
$rub=pg_query("select * from archive where faculty_id='$email';");
if(isset($_GET['status'])){
$indie=$_GET['status'];
}
if($row[5]=='emp'){
    if($sub){
        echo"<h3 style='color:white;'><center>Application rejected by $sub[2] at $sub[4]</h3></center><br>";
        echo"<p style='color:white;'><center>Comment: $sub[3]</p></center><br>";
    }
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <h3 style='color:white;'>Leave application details to fill</h3><br>
    <p style='color:white;'>Beginning date:</p> <input type='date' name='begin' required><br>
    <p style='color:white;'>Ending date:</p> <input type='date' name='end' required><br>
    <input type=\"text\"  name=\"app\" placeholder=\"Comments for taking leave\">
    <input type=\"submit\" name='sub' value=\"Submit\">
    </form><br>;";
    $sub=pg_query("select * from hod_leave where faculty_id='$email';");
    $sub=pg_fetch_row($sub);
    if(($sub)){
       $temp1=pg_query("select hod_comm from msg");
       $temp1=pg_fetch_row($temp1);
       if($temp1[0]!=''){
        echo " <h3 style='color:white;'>$temp1[0]</h3><br>";
       }
    }
    $sub=pg_query("select * from dean_leave where faculty_id='$email';");
    $sub=pg_fetch_row($sub);
    if(($sub)){
        $temp1=pg_query("select dean_comm from msg");
       $temp1=pg_fetch_row($temp1);
       if($temp1[0]!=''){
        echo " <h3 style='color:white;'>$temp1[0]</h3><br>";
       }
    }
    $sub=pg_query("select * from dir_leave where faculty_id='$email';");
    $sub=pg_fetch_row($sub);
    if(($sub)){
        $temp1=pg_query("select dir_comm from msg");
       $temp1=pg_fetch_row($temp1);
       if($temp1[0]!=''){
        echo " <h3 style='color:white;'>$temp1[0]</h3><br>";
       }
    }
        echo"<form action='portal2.php' method=\"get\">
        <input type='hidden' name='username' value='$name'>
        <input type='hidden' name='email' value='$email'>
        <input type='hidden' name='pos' value='$row[5]'>
        <input type='hidden' name='status' value='$indie'>
        <input type=\"text\"  name=\"aa\" placeholder=\"Answer to query\" required>
        <input type=\"submit\" name='rr' value=\"Respond\">
        </form>";
    echo "<h3 style='color:white;'>History of leaves taken</h3><br>"; //History
    echo "<table border='1' cellspacing='0'>
    <tr>
    <th  style=\"color:white;\">Reason</th>
    <th  style=\"color:white;\">year</th>
    <th  style=\"color:white;\">Start date</th>
    <th  style=\"color:white;\">End date</th>
    <th  style=\"color:white;\">Hod comments</th>
    <th  style=\"color:white;\">Dean comments</th>
    <th  style=\"color:white;\">Director comments</th>
    <th  style=\"color:white;\">Timestamp when approved</th>
    </tr>";
while($i=pg_fetch_row($rub)){
    echo"<tr>";
    echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[4] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[5] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[6] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[8] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[9] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[10] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[7] ."</td>";
    echo "</tr>";
}
echo "</table>;
    <form action='portal2.php' method=\"get\">
    <input type=\"submit\" class='btn pull-right sc-contact-submit' name='log' value=\"Logout\">;
    </form>";
}
else if($row[5]=='hod'){
    if($sub){
        echo"<h3 style='color:white;'><center>Application rejected by $sub[2] at $sub[4]</h3></center><br>";
        echo"<p style='color:white;'><center>Comment: $sub[3]</p></center><br>";
    }
    echo "<table border='1' cellspacing='0'>
        <tr>
        <th style=\"color:white;\"> username</th>
        <th  style=\"color:white;\">emailid</th>
        <th  style=\"color:white;\">Reason</th>
        <th style=\"color:white;\"> leaves remaining</th>
        <th  style=\"color:white;\">year</th>
        <th  style=\"color:white;\">leaves applied</th>
        <th  style=\"color:white;\">Start date</th>
        <th  style=\"color:white;\">End date</th>
        <th  style=\"color:white;\">Submitted on</th>
        </tr>";
        $query1="select * from hod_leave where dept_name='$row[2]';";
        $result1=pg_query($query1);
    while($i=pg_fetch_row($result1)){
        echo"<tr>";
        echo"<td  style=\"color:white;\">" . $i[1] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[4] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[5] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[6] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[7] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[8] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[9] ."</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <input type=\"text\" name='ID' placeholder=\"InsID's leave to approve\" required>
    <input type=\"text\" name='comm' placeholder=\"Your comments for approval\" required>
    <input type=\"submit\" name='approve' value=\"Approve\">
    </form>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <input type=\"text\" name='ID' placeholder=\"InsID's leave to reject\" required>
    <input type=\"text\" name='comm' placeholder=\"Your comments for rejection\" required>
    <input type=\"submit\" name='rej' value=\"Reject\">
    </form>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <h3 style='color:white;'>Leave application details to fill</h3><br>
    <p style='color:white;'>Beginning date:</p> <input type='date' name='begin' required><br>
    <p style='color:white;'>Ending date:</p> <input type='date' name='end' required><br>
    <input type=\"text\"  name=\"app\" placeholder=\"Comments for taking leave\">
    <input type=\"submit\" name='sub' value=\"Submit\">
    </form>";
    $sub=pg_query("select * from dir_leave where faculty_id='$email';");
$sub=pg_fetch_row($sub);
if(($sub)){
    $temp1=pg_query("select dir_comm from msg");
   $temp1=pg_fetch_row($temp1);
   if($temp1[0]!=''){
    echo " <h3 style='color:white;'>$temp1[0]</h3><br>";
   }
}
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <input type=\"text\"  name=\"aa\" placeholder=\"Answer to query\" required>
    <input type=\"submit\" name='rr' value=\"Respond\">
    </form>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <input type=\"text\"  name=\"ff\" placeholder=\"Whom to ask queries\" required>
    <input type=\"text\"  name=\"ll\" placeholder=\"Ask for queries\" required>
    <input type=\"submit\" name='cc' value=\"Comment\">
    </form>";
    //Status of leaves taken by faculty
    $lea=pg_query("select * from archive where dept_name='$row[2]';");
    echo "<h3 style='color:white;'>Leaves taken by faculty/ Pending approval</h3><br>";
    echo "<table border='1' cellspacing='0'>
    <tr>
    <th  style=\"color:white;\">Faculty ID</th>
    <th  style=\"color:white;\">Faculty name</th>
    <th  style=\"color:white;\">Reason</th>
    <th  style=\"color:white;\">Start date</th>
    <th  style=\"color:white;\">End date</th>
    <th  style=\"color:white;\">Year</th>
    <th  style=\"color:white;\">Your comments</th>
    <th  style=\"color:white;\">Timestamp of your decision</th>
    <th  style=\"color:white;\">Dean Comments</th>
    <th  style=\"color:white;\">Timestamp Dean's decision</th>
    </tr>";
while($i=pg_fetch_row($lea)){
    if($i[0]==$row[0]){continue;}
    echo"<tr>";
    echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[1] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[3] ."</td>"; //reason
    echo"<td  style=\"color:white;\">" . $i[5] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[6] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[4] ."</td>";  //year
    echo"<td  style=\"color:white;\">" . $i[8] ."</td>"; 
    echo"<td  style=\"color:white;\">" . $i[11] ."</td>"; //approved
    echo"<td  style=\"color:white;\">" . $i[9] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[7] ."</td>";
    echo "</tr>";
}   //approved by dean also
$lea=pg_query("select * from dean_leave where dept_name='$row[2]';");
while($i=pg_fetch_row($lea)){
    echo"<tr>";
    echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[1] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[9] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[10] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[5] ."</td>";  //year
    echo"<td  style=\"color:white;\">" . $i[8] ."</td>"; 
    echo"<td  style=\"color:white;\">" . $i[12] ."</td>"; //approved
    echo"<td  style=\"color:white;\">" . "Pending" ."</td>";
    echo"<td  style=\"color:white;\">" . "N/A" ."</td>";
    echo "</tr>";
}   //approved by hod only
echo "</table>";


//Rejected faculty leaves
$lea=pg_query("select * from rejected where dept_name='$row[2]';");

echo "<h3 style='color:white;'>Leaves currently rejected</h3><br>";
    echo "<table border='1' cellspacing='0'>
    <tr>
    <th  style=\"color:white;\">Faculty ID</th>
    <th  style=\"color:white;\">Rejected by </th>
    <th  style=\"color:white;\">Comments</th>
    <th  style=\"color:white;\">Timestamp</th>
    </tr>";
while($i=pg_fetch_row($lea)){
    echo"<tr>";
    echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[2] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[3] ."</td>"; //reason
    echo"<td  style=\"color:white;\">" . $i[4] ."</td>";
    echo "</tr>";
}  
    echo "</table>";

    //History of leaves taken
    echo "<h3 style='color:white;'>History of leaves taken</h3><br>";
    echo "<table border='1' cellspacing='0'>
    <tr>
    <th  style=\"color:white;\">Reason</th>
    <th  style=\"color:white;\">year</th>
    <th  style=\"color:white;\">Start date</th>
    <th  style=\"color:white;\">End date</th>
    <th  style=\"color:white;\">Director comments</th>
    <th  style=\"color:white;\">Timestamp when approved</th>
    </tr>";
while($i=pg_fetch_row($rub)){
    echo"<tr>";
    echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[4] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[5] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[6] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[10] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[7] ."</td>";
    echo "</tr>";
}
echo "</table>";
//Comments on applied applications
$lea=pg_query("select * from msg;");

echo "<h3 style='color:white;'>Extra comments on leave applications</h3><br>";
    echo "<table border='1' cellspacing='0'>
    <tr>
    <th  style=\"color:white;\">Faculty ID</th>
    <th  style=\"color:white;\">HOD Comments </th>
    <th  style=\"color:white;\">Dean Comments</th>
    <th  style=\"color:white;\">Director Comments</th>
    </tr>";
while($i=pg_fetch_row($lea)){
    echo"<tr>";
    echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[1] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[2] ."</td>"; //reason
    echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
    echo "</tr>";
}  
    echo "</table>";
    echo"<form action='portal2.php' method=\"get\">
    <input type=\"submit\" class='btn pull-right sc-contact-submit' name='log' value=\"Logout\">
    </form>";
}
else if($row[5]=='dir'){
    echo "<table border='1' cellspacing='0'>
        <tr>
        <th style=\"color:white;\"> username</th>
        <th  style=\"color:white;\">emailid</th>
        <th  style=\"color:white;\">Reason</th>
        <th style=\"color:white;\"> leaves remaining</th>
        <th  style=\"color:white;\">year</th>
        <th  style=\"color:white;\">leaves applied</th>
        <th  style=\"color:white;\">Start date</th>
        <th  style=\"color:white;\">End date</th>
        <th  style=\"color:white;\">Prior Comments if any</th>
        <th  style=\"color:white;\">Submitted on</th>
        </tr>";
        $query1="select * from dir_leave;";
        $result1=pg_query($query1);
    while($i=pg_fetch_row($result1)){
        echo"<tr>";
        echo"<td  style=\"color:white;\">" . $i[1] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[4] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[5] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[6] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[7] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[8] ."</td>";
        if($i[10]){
        echo"<td  style=\"color:white;\">" . $i[10] ."</td>";
        }
        else{
        echo"<td  style=\"color:white;\">" . "None" ."</td>";
        }
        echo"<td  style=\"color:white;\">" . $i[9] ."</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type=\"text\" name='ID' placeholder=\"InsID's leave to approve\" required>
    <input type=\"text\" name='comm' placeholder=\"Your comments for approval\" required>
    <input type=\"submit\" name='approve' value=\"Approve\">
    </form>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type=\"text\" name='ID' placeholder=\"InsID's leave to reject\" required>
    <input type=\"text\" name='comm' placeholder=\"Your comments for rejection\" required>
    <input type=\"submit\" name='rej' value=\"Reject\">
    </form>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <input type=\"text\"  name=\"ff\" placeholder=\"Whom to ask queries\">
    <input type=\"text\"  name=\"ll\" placeholder=\"Ask for queries\">
    <input type=\"submit\" name='cc' value=\"Comment\">
    </form>";
    echo "<h3 style='color:white';>Faculty details</h3>";
    echo "<table border='1' cellspacing='0'>
        <tr>
        <th style=\"color:white;\"> emailID</th>
        <th  style=\"color:white;\">Name</th>
        <th  style=\"color:white;\">Department</th>
        <th style=\"color:white;\"> leaves left</th>
        <th  style=\"color:white;\">position</th>
        </tr>";
        $query1="select * from faculty;";
        $result1=pg_query($query1);
    while($i=pg_fetch_row($result1)){
        if($i[5]=='dir'){continue;}
        echo"<tr>";
        echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[1] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[2] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[5] ."</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type=\"text\" name='ch' placeholder=\"Instructor ID\" required>
    <input type=\"submit\"  name='d' value=\"Dean\">
    <input type=\"submit\"  name='h' value=\"HOD\">
    </form>";
    //Comments on applied applications
$lea=pg_query("select * from msg;");

echo "<h3 style='color:white;'>Extra comments on leave applications</h3><br>";
    echo "<table border='1' cellspacing='0'>
    <tr>
    <th  style=\"color:white;\">Faculty ID</th>
    <th  style=\"color:white;\">HOD Comments </th>
    <th  style=\"color:white;\">Dean Comments</th>
    <th  style=\"color:white;\">Director Comments</th>
    </tr>";
while($i=pg_fetch_row($lea)){
    echo"<tr>";
    echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[1] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[2] ."</td>"; //reason
    echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
    echo "</tr>";
}  
    echo "</table>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type=\"submit\" class='btn pull-right sc-contact-submit' name='log' value=\"Logout\">
    </form>";
}
else{
    if($sub){
        echo"<h3 style='color:white;'><center>Application rejected by $sub[2] at $sub[4]</h3></center><br>";
        echo"<p style='color:white;'><center>Comment: $sub[3]</p></center><br>";
    }
    echo "<table border='1' cellspacing='0'>
        <tr>
        <th style=\"color:white;\"> username</th>
        <th  style=\"color:white;\">emailid</th>
        <th  style=\"color:white;\">Department name</th>
        <th  style=\"color:white;\">Reason</th>
        <th style=\"color:white;\"> leaves remaining</th>
        <th  style=\"color:white;\">year</th>
        <th  style=\"color:white;\">leaves applied</th>
        <th  style=\"color:white;\">hod id</th>
        <th  style=\"color:white;\">hod comment</th>
        <th  style=\"color:white;\">Start date</th>
        <th  style=\"color:white;\">End date</th>
        <th  style=\"color:white;\">submitted</th>
        <th  style=\"color:white;\">approved by hod</th>
        </tr>";
        $query1="select * from dean_leave;";
        $result1=pg_query($query1);
    while($i=pg_fetch_row($result1)){
        echo"<tr>";
        echo"<td  style=\"color:white;\">" . $i[1] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[2] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[4] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[5] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[6] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[7] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[8] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[9] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[10] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[11] ."</td>";
        echo"<td  style=\"color:white;\">" . $i[12] ."</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <input type=\"text\" name='ID' placeholder=\"InsID's leave to approve\" required>
    <input type=\"text\" name='comm' placeholder=\"Your comments for approval\" required>
    <input type=\"submit\" name='approve' value=\"Approve\">
    </form>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <input type=\"text\" name='ID' placeholder=\"InsID's leave to reject\" required>
    <input type=\"text\" name='comm' placeholder=\"Your comments for rejection\" required>
    <input type=\"submit\" name='rej' value=\"Reject\">
    </form>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <h3 style='color:white;'>Leave application details to fill</h3><br>
    <p style='color:white;'>Beginning date:</p> <input type='date' name='begin' required><br>
    <p style='color:white;'>Ending date:</p> <input type='date' name='end' required><br>
    <input type=\"text\"  name=\"app\" placeholder=\"Comments for taking leave\">
    <input type=\"submit\" name='sub' value=\"Submit\">
    </form>";
    $sub=pg_query("select * from dir_leave where faculty_id='$email';");
$sub=pg_fetch_row($sub);
if(($sub)){
    $temp1=pg_query("select dir_comm from msg");
   $temp1=pg_fetch_row($temp1);
   if($temp1[0]!=''){
    echo " <h3 style='color:white;'>$temp1[0]</h3><br>";
   }
}
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <input type=\"text\"  name=\"aa\" placeholder=\"Answer to query\" required>
    <input type=\"submit\" name='rr' value=\"Respond\">
    </form>";
    echo"<form action='portal2.php' method=\"get\">
    <input type='hidden' name='username' value='$name'>
    <input type='hidden' name='email' value='$email'>
    <input type='hidden' name='pos' value='$row[5]'>
    <input type='hidden' name='status' value='$indie'>
    <input type=\"text\"  name=\"ff\" placeholder=\"Whom to ask queries\" required>
    <input type=\"text\"  name=\"ll\" placeholder=\"Ask for queries\" required>
    <input type=\"submit\" name='cc' value=\"Comment\">
    </form>";
    //Status of leaves taken by faculty
    echo "<h3 style='color:white'>Leaves applied and pending for approval from HOD</h3>";
    echo "<table border='1' cellspacing='0'>
    <tr>
    <th  style=\"color:white;\">Faculty ID</th>
    <th  style=\"color:white;\">Faculty name</th>
    <th  style=\"color:white;\">Reason</th>
    <th  style=\"color:white;\">Start date</th>
    <th  style=\"color:white;\">End date</th>
    <th  style=\"color:white;\">Year</th>
    <th  style=\"color:white;\">HOD comments</th>
    <th  style=\"color:white;\">Timestamp of HOD decision</th>
    <th  style=\"color:white;\">Your Comments</th>
    <th  style=\"color:white;\">Timestamp Your decision</th>
    </tr>";
$lea=pg_query("select * from hod_leave;");
while($i=pg_fetch_row($lea)){
    echo"<tr>";
    echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[1] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[7] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[8] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[5] ."</td>";  //year
    echo"<td  style=\"color:white;\">" . "Pending" ."</td>"; 
    echo"<td  style=\"color:white;\">" . "N/A" ."</td>"; //approved
    echo"<td  style=\"color:white;\">" . "Pending" ."</td>";
    echo"<td  style=\"color:white;\">" . "N/A" ."</td>";
    echo "</tr>";
}   //pending by hod only
echo "</table>";


//Rejected faculty leaves
$lea=pg_query("select * from rejected;");
echo "<h3 style='color:white;'>Leaves currently applied but rejected</h3><br>";
    echo "<table border='1' cellspacing='0'>
    <tr>
    <th  style=\"color:white;\">Faculty ID</th>
    <th  style=\"color:white;\">Department</th>
    <th  style=\"color:white;\">Rejected by whom</th>
    <th  style=\"color:white;\">Comments</th>
    <th  style=\"color:white;\">Timestamp</th>
    </tr>";
while($i=pg_fetch_row($lea)){
    echo"<tr>";
    echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[1] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[2] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[3] ."</td>"; //reason
    echo"<td  style=\"color:white;\">" . $i[4] ."</td>";
    echo "</tr>";
}  
    echo "</table>";

    echo "<h3 style='color:white;'>History of leaves taken</h3><br>"; //History of leaves
    echo "<table border='1' cellspacing='0'>
    <tr>
    <th  style=\"color:white;\">Reason</th>
    <th  style=\"color:white;\">year</th>
    <th  style=\"color:white;\">Start date</th>
    <th  style=\"color:white;\">End date</th>
    <th  style=\"color:white;\">Director comments</th>
    <th  style=\"color:white;\">Timestamp when approved</th>
    </tr>";
while($i=pg_fetch_row($rub)){
    echo"<tr>";
    echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[4] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[5] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[6] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[10] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[7] ."</td>";
    echo "</tr>";
}
echo "</table>";
//Comments on applied applications
$lea=pg_query("select * from msg;");

echo "<h3 style='color:white;'>Extra comments on leave applications</h3><br>";
    echo "<table border='1' cellspacing='0'>
    <tr>
    <th  style=\"color:white;\">Faculty ID</th>
    <th  style=\"color:white;\">HOD Comments </th>
    <th  style=\"color:white;\">Dean Comments</th>
    <th  style=\"color:white;\">Director Comments</th>
    </tr>";
while($i=pg_fetch_row($lea)){
    echo"<tr>";
    echo"<td  style=\"color:white;\">" . $i[0] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[1] ."</td>";
    echo"<td  style=\"color:white;\">" . $i[2] ."</td>"; //reason
    echo"<td  style=\"color:white;\">" . $i[3] ."</td>";
    echo "</tr>";
}  
    echo "</table>";
    echo"<form action='portal2.php' method=\"get\">
    <input type=\"submit\" class='btn pull-right sc-contact-submit' name='log' value=\"Logout\">;
    </form>";
}
?>

</body>
</html>
