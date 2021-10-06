<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Teacher's leave login Portal</title>
<link rel='stylesheet' a href="style.css"/>
</head>
<body>      
<div class="login-box">
    <div class="header">
    <h2>Login</h2>
    </div>
    <form action="portal.php" method="get">
    <div class="textbox">
    <!--<label for='username'>Username :</label>-->
    <i class="fas fa-user"></i>
    <input type="text" placeholder="Username" name="username">
    </div>
    
    <div class="textbox">
    <!--<label for='email'>Email :</label>-->
    <input type="email" placeholder="Email" name="email">
    </div>

    <div class="textbox">
    <!--<label for='password'>Password :</label>-->
    <input type="password" placeholder="Password" name="password">
    </div>

    <input type="submit" name="login" class="btn" value="Sign in">

    <!--<p>Not a user?<a href="registration.php"><b>Register Here</b></a></p>-->
    </form>
    </div>
<?php
//$name=$_GET["username"];
//$con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("Could not connect to server\n");
//$query="select * from faculty;";
//$result=pg_query($query);
//if(isset())
/*$host = "localhost";
$user = "postgres";
$pass = "assassinwifi";
$db = "postgres";
$flag=false;
$con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("Could not connect to server\n");
$query="select * from faculty;";
$result=pg_query($query);
$name=$_GET['username'];
$email=$_GET['email'];
$password=$_GET['password'];
while($row=pg_fetch_row($result)){
    if($row[0]==$name && $row[1]==$email && $row[2]==$password){
        $flag=true;
    }
}
if($flag){
    echo "<br>Login Successful<br>";
}else{
    echo "Either Incorrect credentials have been entered or user not registered<br>";
    echo "Please contact IT section";
}
*/
?>

</body>
</html>