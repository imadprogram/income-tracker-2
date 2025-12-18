<?php 
session_start();
include('connect.php');


if(!isset($_SESSION['temp_id'])){
    header('Location: index.php');
    exit();
}
try{
    if(isset($_POST['verify'])){
        if(!empty($_POST['otp_num'])){
            $entered_otp = $_POST['otp_num'];
            $user_id = $_SESSION['temp_id'];
    
            $result = mysqli_query($connect, "SELECT * FROM users WHERE otp_code = {$entered_otp} AND id = {$user_id}");

            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_assoc($result);
                
                $_SESSION['user-id'] = $user_id;

                unset($_SESSION['temp_id']);
                mysqli_query($connect, "UPDATE users SET otp_code = NULL WHERE id = {$user_id}");

                header('Location: mainCard.php');
                exit();
            }else{
                echo "wrong code !";
            }
        }else {
            echo "fill the input !";
        }
    }
}catch(mysqli_sql_exception){
    echo "wrong input :(";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify the otp</title>
</head>
<body>
    <form action="verify_otp.php" method="POST">
        <input type="text" name="otp_num" placeholder="xxxxxx">
        <input type="submit" name="verify" value="verify">
    </form>
</body>
</html>