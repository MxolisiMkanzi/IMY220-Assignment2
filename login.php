<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false


    if(isset($_POST['submit'],$_POST["email"], $_POST["password"], $_POST["picToUpload"])){
        echo "inside";
        $email = $_POST["email"];
        $pass = $_POST["password"];
        $query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
        $user_id = 0;
        $res = $mysqli->query($query);
        if($row = mysqli_fetch_array($res)){
            $user_id = $row['user_id'];
        }

        $uploadFile = $_FILES["picToUpload"];
        $target_dir = "gallery/";
        for($i =0; i < count($uploadFile["name"]); $i++)
        {
            $target_file = $target_dir . basename($uploadFile[$i]["name"]);
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

            if($uploadFile[$i]["type"] == "image/jpeg"  || $uploadFile[$i]["type"] == "image/jpg"|| $uploadFile[$i]["size"] < 1000000){
                if(move_uploaded_file($uploadFile[$i]["tmp_name"], $target_file))//echo "The file " . basename($uploadFile[$i]["name"]) . "has been uploaded.";
                    $var = 0;
                else
                    echo "Sorry, there was an error uploading your file.";

                $filename = $uploadFile[$i]["tmp_name"];
                $query = "INSERT INTO tbgallery (user_id, filename) VALUES ('$filename', '$user_id');";


                $res = mysqli_query($mysqli, $query) == true;
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Name Surname">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>
							<h1>Image Gallery</h1>
							
							<div class='row ImageGallery'>";
					            $uid = $row['user_id'];
                                $query = "SELECT * FROM tbgallery WHERE user_id = '$uid'";
                                $user_id = 0;
                                $result = $mysqli->query($query);
                                while($image = mysqli_fetch_array($result)){
                                    $iname = &$image['filename'];
                                    $fname = "";
                                    if (file_exists ('gallery/'.$iname.'jpg')) {
                                        $fname = 'gallery/'.$iname .'jpg';
                                    }
                                    else if(file_exists ('gallery/'.$iname.'jpeg')){
                                        $fname = 'gallery/'.$iname .'jpeg';
                                    }

                                    echo "<div class='col-3' style='background-image: url($fname)'>
                                          </div>";
                                }
                            echo "</div>
							";

					$self = htmlspecialchars($_SERVER["PHP_SELF"]);
					echo 	"<form action='$self' method='post' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' multiple='multiple' /><br/>
									<input type='hidden' name='email' id='email' value='$email'/>
									<input type='hidden' name='password' id='password' value='$pass'>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
									
								</div>
						  	</form>";
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in'. $email . $password .'
	  					</div>';
			}
		?>
	</div>
</body>
</html>