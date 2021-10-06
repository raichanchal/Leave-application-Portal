<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Teacher's leave login Portal</title>
<link rel='stylesheet' a href="style.css"/>
</head>
<body>
<?php
$host = "localhost";
$user = "postgres";
$pass = "assassinwifi";
$db = "postgres";
$flag=false;
$con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("Could not connect to server\n");
$query1="select * from log;";
$result1=pg_query($query1);
$name=$_GET['username'];
$email=$_GET['email'];
$password=$_GET['password'];
$flag1=0;
pg_query("select auto_cancell();");
//$today=pg_query("select now();");
//$today=pg_fetch_row($today);
//$t=pg_query("select date(now());");
//$t=pg_fetch_row($t);
//if($t[0]<$today[0]){
//echo "<h3 style='color:white';>yes hello</h3>";
//}
$indie=0;
while($row=pg_fetch_row($result1)){
    if($row[0]==$name && $row[1]==$email && $row[2]==$password){
        $flag=true;
    }
}
if($flag){
    echo "<center><h4 style=\"color:white;\">Login Successful</h4></center>";
    echo "<center><h3 style=\"color:white;\">Welcome $name</h3><br></center>";
    //header('Location:aa.php');
}else{
    header('Location:aa.php');
    //echo"<form action='aa.php' method=\"get\"></form>";
}
$query2="select * from faculty where faculty_id='$email';";
$result2=pg_query($query2);
$row=pg_fetch_row($result2);
echo "<p style='text-align:right;'><h4 style=\"color:white;\">Department :'$row[2]'</h4></p>";
if($row[3]<0){$flag1=-$row[3];$row[3]=0;}
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
$sub=pg_query("select * from rejected where faculty_id='$email';");
$sub=pg_fetch_row($sub);
$rub=pg_query("select * from archive where faculty_id='$email';");
//echo $row[1]
if($flag1!=0){
    echo "<center><h4 style=\"color:white;\">You have applied for $flag1 more leaves than allowed this year</h4></center>";
}
if ($row[5]=='hod'){
    if($sub){
        echo"<h3 style='color:white;'><center>Application rejected by $sub[2] at $sub[4]</h3></center><br>";
        echo"<h4 style='color:white;'><center>Comment: $sub[3]</h4></center><br>";
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
$lea=pg_query("select * from msg where dept_name='$row[2]';");

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
else if($row[5]=='dean'){
    if($sub){
        echo"<h3 style='color:white;'><center>Application rejected by $sub[2] at $sub[4]</h3></center><br>";
        echo"<h4 style='color:white;'><center>Comment: $sub[3]</h4></center><br>";
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
    if($i[0]==$row[0]){continue;}
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
else{      //insert into hod_leave values (faculty_id,faculty_name,dept_name,reason_of_leave,leave_left,	curr_year,leave_applied);  //emp
    if($sub){
        echo"<h3 style='color:white';><center>Application rejected by $sub[2] at $sub[4]</h3></center><br>";
        echo"<h4 style='color:white';><center>Comment: $sub[3]</h4></center><br>";
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
?>

</body>
</html>
