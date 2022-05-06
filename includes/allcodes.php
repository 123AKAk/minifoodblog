<?php
    session_start();
    include_once 'connect.php';

    //delete user
    if(isset($_GET['deleteuserid']) && isset($_GET['pagename']))
    {
        $userid = $_GET['deleteuserid'];
        // sql to delete a record
        $sql = "DELETE FROM user WHERE Id= '$userid'";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "User Deleted";
            header("Location: ".$_GET['pagename']);
        } else {
            $_SESSION['failed'] = "Error: " . mysqli_error($conn);
            header("Location: ".$_GET['pagename']);
        }
        $conn->close();
    }
    //approve user
    if(isset($_GET['approveid']) && isset($_GET['pagename']))
    {
        $userid = $_GET['approveid'];
        $sql = "UPDATE `user` SET `Status`='1' WHERE Id = '$userid'";
        if (mysqli_query($conn, $sql)){
            $_SESSION['success'] = "User Approved Successfully!";
            header("Location: ".$_GET['pagename']);
        }
        else{
            $_SESSION['failed'] = "Error: " . mysqli_error($conn);
            header("Location: ".$_GET['pagename']);
        }
        mysqli_close($conn);
    }

    //delete post
    if(isset($_GET['deletepostid']) && isset($_GET['pagename']))
    {
        $postid = $_GET['deletepostid'];
        // sql to delete a record
        $sql = "DELETE FROM blog WHERE Id='$postid'";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "Blog Post Deleted";
            header("Location: ".$_GET['pagename']);
        } else {
            $_SESSION['failed'] = "Error: " . mysqli_error($conn);
            header("Location: ".$_GET['pagename']);
        }
        $conn->close();
    }

    //add post
    if(isset($_POST['submitpost']))
    {
        $userid = $_POST['userid'];
        $location = "../".$_POST['location'];
        $description = $_POST['description'];
        $categoryid = $_POST['categoryid'];
        $content = $_POST['content'];
        $title = $_POST['title'];
        $image = $_FILES["image"]["name"];

        if(empty($description) || empty($categoryid) || empty($content) || empty($title) || empty($image))
        {
            $_SESSION['failed'] = "Fill all Feilds";
            header("Location: ".$location);
        }
        else
        {
            $sql = "SELECT * FROM category WHERE Title = '$categoryid'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc()) {
                    $acategoryid = $row["Id"];
                }
            }
            else
            {
                $asql = "INSERT INTO `category`(`Description`, `Title`) VALUES ('$description', '$categoryid')";
                if (mysqli_query($conn, $asql))
                {
                    $aresult = $conn->query($sql);
                    if ($aresult->num_rows > 0)
                    {
                        while($arow = $aresult->fetch_assoc()) 
                        {
                            $acategoryid = $arow["Id"];
                        }
                    }
                }
                else
                {
                    $_SESSION['failed'] = "Error: " . $sql . ":-" . mysqli_error($conn);
                    header("Location: ".$location);
                }
            }
            $newimage = processImage("image");
            if(!empty($newimage))
            {
                $sql = "INSERT INTO `blog`(`UserId`, `Description`, `CategoryId`, `Content`, `Title`, `Image`) VALUES ('$userid', '$description', '$acategoryid', '$content', '$title', '$newimage')";
                if (mysqli_query($conn, $sql)) 
                {
                    $_SESSION['success'] = "New Post has been added successfully!";
                    header("Location: ".$location);
                }
                else
                {
                    $_SESSION['failed'] = "Error: " . $sql . ":-" . mysqli_error($conn);
                    header("Location: ".$location);
                }
                mysqli_close($conn);
            }
            else
            {
                $_SESSION['failed'] = "Error Uploading Image";
                header("Location: ".$location);
            }
        }
    }

    //login user/admin
    if(isset($_POST['loginsubmit']))
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if(empty($email) || empty($password))
        {
            $_SESSION['failed'] = "Fill all Feilds";
            header("Location: ../login.php");
        }
        else
        {
            $sql = "SELECT * FROM user WHERE Email = '$email' AND Password = '$password' ";
            $result = $conn->query($sql);
            if ($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc()) {
                   $role = $row["RoleId"];
                   $userid = $row["Id"];
                   $status = $row["Status"];
                }
                if($status == 0){
                    $_SESSION['failed'] = "Login Failed: Admin has not Approved Account";
                    header("Location: ../login.php");
                }
                else{
                    if($role == "1"){
                        $_SESSION['admin'] = $userid;
                        header("Location: ../admin.php");
                    }
                    else{
                        $_SESSION['user'] = $userid;
                        header("Location: ../dashboard.php");
                    }
                }
            }
            else{
                $_SESSION['failed'] = "Login Failed: " . mysqli_error($conn);
                header("Location: ../login.php");
            }
        }
    }
    // register user
    if(isset($_POST['registersubmit']))
    {
        $roleId = $_POST['roleid'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $profile_img = $_FILES["profile_img"]["name"];

        if(empty($roleId) || empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($profile_img))
        {
            $_SESSION['failed'] = "Fill all Feilds";
            header("Location: ../register.php");
        }
        else
        {
            $sql = "SELECT * FROM user WHERE Email = '$email'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0)
            {
                $_SESSION['failed'] = "Register Failed: This Email is Already Associated with an Account";
                header("Location: ../register.php");
            }
            else{
                $newprofile_img = processImage('profile_img');
                if(!empty($newprofile_img))
                {
                    $sql = "INSERT INTO `user`(`RoleId`, `Firstname`, `Lastname`, `Email`, `Password`, `Profile_img`, `Status`) VALUES ('$roleId', '$firstname', '$lastname', '$email', '$password', '$newprofile_img', '0')";
                    if (mysqli_query($conn, $sql))
                    {
                        $_SESSION['success'] = "Account Created";
                        header("Location: ../register.php");
                    }
                    else
                    {
                        $_SESSION['failed'] = "Error: " . mysqli_error($conn);
                        header("Location: ../register.php");
                    }
                    mysqli_close($conn);
                }
            }
        }
    }

    //update profile
    if(isset($_POST['updatesubmit']))
    {
        $userid = $_POST['userid'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $profile_img = $_FILES["profile_img"]["name"];

        if(!empty($profile_img))
        {
            $newprofile_img = processImage('profile_img');
            if(!empty($newprofile_img))
            {
                $sql = "UPDATE `user` SET `Firstname`='$firstname', `Lastname`='$lastname', `Email`='$email', `Password`='$password', `Profile_img`='$newprofile_img' WHERE Id = '$userid'";
                if (mysqli_query($conn, $sql))
                {
                    $_SESSION['success'] = "Account Updated";
                    header("Location: ../profile.php?id=".$userid);
                }
                else
                {
                    $_SESSION['failed'] = "Error: " . mysqli_error($conn);
                    header("Location: ../profile.php?id=".$userid);
                }
                mysqli_close($conn);
            }
        }
        else
        {
            $sql = "UPDATE `user` SET `Firstname`='$firstname', `Lastname`='$lastname', `Email`='$email', `Password`='$password' WHERE Id = '$userid'";
            if (mysqli_query($conn, $sql))
            {
                $_SESSION['success'] = "Account Updated";
                header("Location: ../profile.php?id=".$userid);
            }
            else
            {
                $_SESSION['failed'] = "Error: " . mysqli_error($conn);
                header("Location: ../profile.php?id=".$userid);
            }
            mysqli_close($conn);
        }
    }

    //edit post
    if(isset($_POST['editpost']))
    {
        $postid = $_POST['postid'];
        $userid = $_POST['userid'];

        $description = $_POST['description'];
        $categoryid = $_POST['categoryid'];
        $content = $_POST['content'];
        $title = $_POST['title'];
        $image = $_FILES["image"]["name"];

        $sql = "SELECT * FROM category WHERE Title = '$categoryid'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0)
        {
            while($row = $result->fetch_assoc()) {
                $acategoryid = $row["Id"];
            }
        }
        else
        {
            $asql = "INSERT INTO `category`(`Description`, `Title`) VALUES ('$description', '$categoryid')";
            if (mysqli_query($conn, $asql))
            {
                $aresult = $conn->query($sql);
                if ($aresult->num_rows > 0)
                {
                    while($arow = $aresult->fetch_assoc()) 
                    {
                        $acategoryid = $arow["Id"];
                    }
                }
            }
            else
            {
                $_SESSION['failed'] = "Error: " . $sql . ":-" . mysqli_error($conn);
                header("Location: ../editpost.php?postid=".$postid."&userid=".$userid);
            }
        }

        $newimage = processImage("image");
        if(!empty($newimage))
        {
            $sql = "UPDATE `blog` SET `Description`='$description', `CategoryId`='$acategoryid', `Content`='$content', `Title`='$title', `Image`='$newimage' WHERE Id = '$postid'";
            if (mysqli_query($conn, $sql)) 
            {
                $_SESSION['success'] = "Post Updated!";
                header("Location: ../editpost.php?postid=".$postid."&userid=".$userid);
            }
            else
            {
                $_SESSION['failed'] = "Error: " . $sql . ":-" . mysqli_error($conn);
                header("Location: ../editpost.php?postid=".$postid."&userid=".$userid);
            }
            mysqli_close($conn);
        }
        else
        {
            $sql = "UPDATE `blog` SET `Description`='$description', `CategoryId`='$acategoryid', `Content`='$content', `Title`='$title' WHERE Id = '$postid'";
            if (mysqli_query($conn, $sql)) 
            {
                $_SESSION['success'] = "Post Updated!";
                header("Location: ../editpost.php?postid=".$postid."&userid=".$userid);
            }
            else
            {
                $_SESSION['failed'] = "Error: " . $sql . ":-" . mysqli_error($conn);
                header("Location: ../editpost.php?postid=".$postid."&userid=".$userid);
            }
            mysqli_close($conn);
        }
    }

    //upload image
    function processImage($image)
	{
		$filename = htmlspecialchars(basename($_FILES[$image]["name"]));
		$target_dir = "../uploads/";
		$target_file = $target_dir.basename($_FILES[$image]["name"]);
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
						
		// Check file size
		if ($_FILES[$image]["size"] > 500000) 
		{
			$filename = "";
			return $filename;
		}
		// Allow certain file formats
		else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) 
		{
			$filename = "";
			return $filename;
		}
		//upload file
		move_uploaded_file($_FILES[$image]["tmp_name"], $target_file);
		return $filename;
	}

    //add comment
    if(isset($_POST['commentsubmit']))
    {
        $postid = $_POST['postid'];
        $comment = $_POST['comment'];

        if(empty($comment))
        {
            $_SESSION['failed'] = "Fill Comment Feild";
            header("Location: ../single.php?productid=".$postid);
        }
        else
        {
            if(isset($_SESSION['admin']))
            {
                $userid = $_SESSION['admin'];

                $ssql = "SELECT * FROM user WHERE Id='$userid'";
                $sresult = $conn->query($ssql);
                if ($sresult->num_rows > 0) 
                {
                  // output data of each row
                  while($srow = $sresult->fetch_assoc()) 
                  {
                    $name = $srow["Firstname"];
                  }
                }
                $description = "Posted By ".$name;

                $sql = "INSERT INTO `comment`(`UserId`, `Text`, `Description`) VALUES ('$userid', '$comment', '$description')";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success'] = "Comment Added";
                    header("Location: ../single.php?productid=".$postid);
                }
                else{

                }
            }
            else if(isset($_SESSION['user']))
            {
                $userid = $_SESSION['user'];

                $ssql = "SELECT * FROM user WHERE Id='$userid'";
                $sresult = $conn->query($ssql);
                if ($sresult->num_rows > 0) 
                {
                  // output data of each row
                  while($srow = $sresult->fetch_assoc()) 
                  {
                    $name = $srow["Firstname"];
                  }
                }
                $description = "Posted By ".$name;

                $sql = "INSERT INTO `comment`(`UserId`, `Text`, `Description`) VALUES ('$userid', '$comment', '$description')";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success'] = "Comment Added";
                    header("Location: ../single.php?productid=".$postid);
                }
                else{

                }
            }
            else
            {
                $_SESSION['failed'] = "Login to Comment";
                header("Location: ../single.php?productid=".$postid);
            }
        }
    }    

?>