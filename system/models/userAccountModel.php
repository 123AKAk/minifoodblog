<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Users
{
	function connection(){return new Database();}
	
	function SharedComponents(){return new SharedComponents();}

	function users()
	{
        $UserData =  new stdClass();
		$UserData->EncryptedId = "";
        $UserData->Email = "";
        $UserData->Password = "";
        $UserData->Fullname = "";
        $UserData->Address = "";
        $UserData->Town_City = "";
        $UserData->Country = "";
        $UserData->ZipCode = "";
		$UserData->Contact_Info = "";
		$UserData->OrderNote = "";
        return $UserData;
    }

	function getUsers()
    {
		$db_handle = $this->connection()->open();
        try
		{
            $statement = $db_handle->query("SELECT * FROM users ORDER BY id DESC");
            $users = $statement->fetchAll(PDO::FETCH_ASSOC);

            $UserData = $this->users();
            $Listusers[] = ($UserData);

            if (!empty($users))
            {
                foreach($users as $item)
                {
                    $users->EncryptedId = $this->SharedComponents()->protect($item["id"]);
                    $users->Email = $item["email"];
                    $users->Password = $item["password"];
                    $users->Fullname = $item["fullname"];
                    $users->Address = $item["address"];
                    $users->Town_City = $item["town_city"];
                    $users->Country = $item["country"];
                    $users->ZipCode = $item["zipcode"];
                    $users->Contact_Info = $item["contact_info"];
                    $users->OrderNote = $item["ordernote"];
                    
                    array_push($Listusers, $users);
                }
                return $Listusers;
            }
        }
		catch(PDOException $ex)
		{
			return $ex;
		}
		finally
		{
			$this->connection()->close();
		}
    }

	function getUserById($idds)
    {
        $db_handle = $this->connection()->open();
        try
		{
            $astmt = $db_handle->prepare("SELECT * FROM users WHERE id=:id");
            $astmt->execute(['id' => $this->SharedComponents()->unprotect($idds)]);
            $item = $astmt->fetch();

            if(!empty($item))
            {
                $UserData = $this->users();
                $users = new $UserData;
                
                $users->EncryptedId = $this->SharedComponents()->protect($item["id"]);
                $users->Email = $item["email"];
                $users->Password = $item["password"];
                $users->Fullname = $item["fullname"];
                $users->Address = $item["address"];
                $users->Town_City = $item["town_city"];
                $users->Country = $item["country"];
                $users->ZipCode = $item["zipcode"];
                $users->Contact_Info = $item["contact_info"];
                $users->OrderNote = $item["ordernote"];
                
                return $users;
            }
        }
		catch(PDOException $ex)
		{
			return $ex;
		}
		finally
		{
			$this->connection()->close();
		}
    }

    function verifyUser($email, $password)
    {
        session_start();
        $db_handle = $this->connection()->open();
        try
		{
            $stmt = $db_handle->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
			$stmt->execute(['email'=>$email]);
			$row = $stmt->fetch();
			if($row['numrows'] > 0)
            {
                if(password_verify($password, $row['password']))
                {
                    if($row['type'] == 1)
                    {
                        $_SESSION['admin'] = $row['id'];
                    }
                    else
                    {
                        $_SESSION['user'] = $row['id'];
                    }
                    $result = ['response' => true, 'message' => 'Login Successful'];
                }
                else
                {
                    $result = ['response' => false, 'message' => 'Incorrect Password'];
                }
			}
			else
            {
                $result = ['response' => false, 'message' => 'Email not found'];
			}

            session_write_close();
            return $result;
        }
		catch(PDOException $ex)
		{
			return $ex;
		}
		finally
		{
			$this->connection()->close();
		}
    }

    function registerUserAccount($username, $email, $password)
    {
        $db_handle = $this->connection()->open();
        try
		{
            $stmt = $db_handle->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
			$stmt->execute(['email'=>$email]);
			$row = $stmt->fetch();
			if($row['numrows'] > 0)
            {
                $result = ['response' => false, 'message' => 'Email already taken'];
			}
			else
            {
				$apassword = password_hash($password, PASSWORD_DEFAULT);

                //generate code
				$set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$code = substr(str_shuffle($set), 0, 12);

				//generate code
                $stmt = $db_handle->prepare("INSERT INTO users (fullname, email, password, activate_code) VALUES (:fullname, :email, :password, :activate_code)");
                $stmt->execute(['fullname'=>$username, 'email'=>$email, 'password'=>$apassword, 'activate_code'=>$code]);
                $userid = $this->getLastInsertedUserId($email);
                $userid = $this->SharedComponents()->protect($userid);

                $message = "
                    <h3>Thank you for Registering on Annibel Jewelry & Collections.</h3>
                    <p>Your Account Information:</p>
                    <p>Email: ".$email."</p>
                    <p>Password: ".$password."</p>
                    <p>Please click the link below to activate your account.</p>
                    <a href='http://localhost:8081/files/activate.php?code=".$code."&user=".$userid."'>Activate Account</a>
                    <p>And don't forget to order from Us</p>
                ";

                //Load Composer's autoloader
                require '../../vendor/autoload.php';

                //Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);
                try
                {
                    //$_SESSION['user'] = $userid;

                    //Enable verbose debug output

                    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;

                    //Send using SMTP
                    $mail->isSMTP();
                    //Set the SMTP server to send through
                    $mail->Host = 'smtp.gmail.com';
                    //Enable SMTP authentication
                    $mail->SMTPAuth = true;
                    $mail->Username = 'favourakak@gmail.com';
                    //SMTP password
                    $mail->Password = 'Iakak 1!A,,';
                    //Enable implicit TLS encryption
                    $mail->SMTPSecure = 'tls';
                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                    $mail->Port = 587;

                    //Recipients
                    $mail->setFrom('favourakak@gmail.com', 'Annibel Jewelry & Collections');
                    //Add a recipient
                    $mail->addAddress($email, $username);
                    //Name is optional
                    //$mail->addAddress('favourakak@gmail.com');
                    $mail->addReplyTo('favourakak@gmail.com', 'For any Information');
                    $mail->addCC('favourakak@gmail.com');
                    //$mail->addBCC('favourakak@gmail.com');
                    
                    //Add attachments
                    // $mail->addAttachment('/var/tmp/file.tar.gz');
                    //Optional name
                    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');

                    //Content
                    //Set email format to HTML
                    $mail->isHTML(true);
                    $mail->Subject = 'Registration Successful Annibel Jewelry & Collections';
                    $mail->Body    = $message;
                    $mail->AltBody = "Thank you for Registering on Annibel Jewelry & Collections | Your Account Information: Email - ".$email.", Password: ".$password." Please don't forget to order from Us";

                    $mail->send();

                    $result = ['response' => true, 'message' => 'Account created Successful, access your email to activate your account'];
                }
                catch (Exception $eax) 
                {
                    return $result = ['response' => false, 'message' => 'EMAIL SENDING FAILED. INFO: '.$mail->ErrorInfo];
                }
            }
            return $result;
        }
		catch(PDOException $ex)
		{
			return $result = ['response' => false, 'message' => $ex];
		}
		finally
		{
			$this->connection()->close();
		}
    }

    function updateUserAccount($idds, $fullname, $email, $address, $town_city, $country, $zipcode, $contact_info, $ordernote)
    {
        $db_handle = $this->connection()->open();
        try
		{
            $decryptedId = $this->SharedComponents()->unprotect($idds);

            $stmt = $db_handle->prepare("UPDATE users SET fullname=:fullname, email=:email, address=:address, town_city=:town_city, country=:country, zipcode=:zipcode, contact_info=:contact_info, ordernote=:ordernote WHERE id = $decryptedId");
			$stmt->execute(['fullname'=>$fullname, 'email'=>$email, 'address'=>$address, 'town_city'=>$town_city, 'country'=>$country, 'zipcode'=>$zipcode, 'contact_info'=>$contact_info, 'ordernote'=>$ordernote]);
            
            $result = ['response' => true, 'message' => 'User '.$fullname.' Account Updated'];
            
            return $result;
        }
		catch(PDOException $ex)
		{
			return $result = ['response' => false, 'message' => $ex];
		}
		finally
		{
			$this->connection()->close();
		}
    }

    function changePassword($idds, $password)
    {
        $db_handle = $this->connection()->open();
        try
		{
            $decryptedId = $this->SharedComponents()->unprotect($idds);

            $apassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db_handle->prepare("UPDATE users SET password=:password WHERE id = $decryptedId");
			$stmt->execute(['password'=>$apassword]);
            
            $result = ['response' => true, 'message' => 'Password Updated'];
            
            return $result;
        }
		catch(PDOException $ex)
		{
			return $result = ['response' => false, 'message' => $ex];
		}
		finally
		{
			$this->connection()->close();
		}
    }

    //create and add biiling info through checkout page
    function registerUserAccount_cart($email, $password, $fullname, $address, $town_city, $country, $zipcode, $contact_info, $ordernote)
    {
        $db_handle = $this->connection()->open();
        try
		{
            $stmt = $db_handle->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
			$stmt->execute(['email'=>$email]);
			$row = $stmt->fetch();
			if($row['numrows'] > 0)
            {
                $result = ['response' => false, 'message' => 'Email already taken'];
			}
			else
            {
				$apassword = password_hash($password, PASSWORD_DEFAULT);

                //generate code
				$set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$code = substr(str_shuffle($set), 0, 12);

				//generate code
                $stmt = $db_handle->prepare("INSERT INTO users (fullname, email, password, address, town_city, country, zipcode, contact_info, ordernote, activate_code) VALUES (:fullname, :email, :password, :address, :town_city, :country, :zipcode, :contact_info, :ordernote, :activate_code)");
                $stmt->execute(['fullname'=>$fullname, 'email'=>$email, 'password'=>$apassword, 'address'=>$address, 'town_city'=>$town_city, 'country'=>$country, 'zipcode'=>$zipcode, 'contact_info'=>$contact_info, 'ordernote'=>$ordernote, 'activate_code'=>$code]);

                $userid = $this->getLastInsertedUserId($email);
                $userid = $this->SharedComponents()->protect($userid);

                $message = "
                    <h3>Thank you for Registering on Annibel Jewelry & Collections.</h3>
                    <p>Your Account Information:</p>
                    <p>Email: ".$email."</p>
                    <p>Password: ".$password."</p>
                    <p>Please click the link below to activate your account.</p>
                    <a href='http://localhost:8081/files/activate.php?code=".$code."&user=".$userid."'>Activate Account</a>
                    <p>And don't forget to order from Us</p>
                ";

                require '../../vendor/autoload.php';
                
                $mail = new PHPMailer(true);
                try
                {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'favourakak@gmail.com';
                    $mail->Password = 'Iakak 1!A,,';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    //Recipients
                    $mail->setFrom('favourakak@gmail.com', 'Annibel Jewelry & Collections');
                    $mail->addAddress($email, $fullname);
                    $mail->addReplyTo('favourakak@gmail.com', 'For any Information');
                    $mail->addCC('favourakak@gmail.com');
                    
                    $mail->isHTML(true);
                    $mail->Subject = 'Registration Successful Annibel Jewelry & Collections';
                    $mail->Body    = $message;
                    $mail->AltBody = "Thank you for Registering on Annibel Jewelry & Collections | Your Account Information: Email - ".$email.", Password: ".$password." Please don't forget to order from Us";

                    $mail->send();

                    $result = ['response' => true, 'message' => 'Account created Successful, access your email to activate your account', 'usercode' => $userid];
                }
                catch (Exception $eax) 
                {
                    return $result = ['response' => false, 'message' => 'EMAIL SENDING FAILED. INFO: '.$mail->ErrorInfo, 'usercode' => $userid];
                }
            }
            return $result;
        }
		catch(PDOException $ex)
		{
			return $result = ['response' => false, 'message' => $ex, 'usercode' => $userid];
		}
		finally
		{
			$this->connection()->close();
		}
    }

    function getLastInsertedUserId($email)
    {
        $db_handle = $this->connection()->open();
		try
		{
			$astmt = $db_handle->prepare("SELECT * FROM users WHERE email=:email");
        	$astmt->execute(['email' => $email]);
			$userid = $astmt->fetch();

            //$_SESSION['user'] = $userid["id"];

			return $userid["id"];
		}
		catch(PDOException $ex)
		{
			return $ex;
		}
		finally
		{
			$this->connection()->close();
		}
    }

    function activateUser($code, $userid)
    {
        $db_handle = $this->connection()->open();
		try
		{
            $idds = $this->SharedComponents()->unprotect($userid);

            $stmt = $db_handle->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE activate_code=:code AND id=:id");
            $stmt->execute(['code'=>$code, 'id'=>$idds]);
            $row = $stmt->fetch();

            if($row['numrows'] > 0)
            {
                if($row['status'] == 1)
                {
                    return $output = '
                        <br>
                        <br>
                        <div class="alert alert-danger">
                            <h4><i class="icon fa fa-warning"></i> Error!</h4>
                            Account already activated.
                        </div>
                        <p>You may <a href="login.php">Login</a> or back to <a href="index.php">Homepage</a>.</p>
                    ';
                }
                else
                {
                    try
                    {
                        $stmt = $db_handle->prepare("UPDATE users SET status=:status WHERE id=:id");
                        $stmt->execute(['status'=>1, 'id'=>$idds]);
                        return $output = '
                            <br>
                            <br>
                            <div class="alert alert-success">
                                <h4><i class="icon fa fa-check"></i> Success!</h4>
                                Account activated - Email: <b>'.$row['email'].'</b>.
                            </div>
                            <p>You may <a href="login.php">Login</a> or back to <a href="index.php">Homepage</a>.</p>
                        ';
                    }
                    catch(PDOException $e)
                    {
                        return $output = '
                            <br>
                            <br>
                            <div class="alert alert-danger">
                                <h4><i class="icon fa fa-warning"></i> Error!</h4>
                                '.$e->getMessage().'
                            </div>
                            <p>You may <a href="register.php">Signup</a> or back to <a href="index.php">Homepage</a>.</p>
                        ';
                    }

                }
                
            }
            else{
                return $output = '
                    <div class="alert alert-danger">
                        <h4><i class="icon fa fa-warning"></i> Error!</h4>
                        Cannot activate account. Wrong code.
                    </div>
                    <p>You may <a href="register.php">Signup</a> or back to <a href="index.php">Homepage</a>.</p>
                ';
            }
        }
        catch(PDOException $ex)
		{
			return $ex;
		}
		finally
		{
			$this->connection()->close();
		}
    }

    function checkactivateUser($code, $userid)
    {
        $db_handle = $this->connection()->open();
		try
		{
            $idds = $this->SharedComponents()->unprotect($userid);

            $stmt = $db_handle->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE activate_code=:code AND id=:id");
            $stmt->execute(['code'=>$code, 'id'=>$idds]);
            $row = $stmt->fetch();

            if($row['numrows'] > 0)
            {
                if($row['status'] == 1)
                {
                    return $output = ['response' => false, 'message' => 'Already Activated'];
                }
                else
                {
                    try
                    {
                        $stmt = $db_handle->prepare("UPDATE users SET status=:status WHERE id=:id");
                        $stmt->execute(['status'=>1, 'id'=>$idds]);

                        return $output = ['response' => false, 'message' => 'Account Activated'];
                    }
                    catch(PDOException $ex)
                    {
                        return $output = ['response' => false, 'message' => 'Error Connecting to server'.$ex];
                    }

                }
                
            }
            else
            {
                return $output = ['response' => false, 'message' => 'Cannot activate account. Wrong code.'];
            }
        }
        catch(PDOException $ex)
		{
			return $ex;
		}
		finally
		{
			$this->connection()->close();
		}
    }

    function passwordreset($email)
    {
        $db_handle = $this->connection()->open();
        try
		{
            
            $stmt = $db_handle->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email=:email");
			$stmt->execute(['email'=>$email]);
			$row = $stmt->fetch();
			if($row['numrows'] > 0)
            {
                //generate code
				$set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$code = substr(str_shuffle($set), 0, 12);

				$stmt = $db_handle->prepare("UPDATE users SET activate_code=:code WHERE id=:id");
				$stmt->execute(['code'=>$code, 'id'=>$row['id']]);
				
				$message = "
					<h2>Password Reset</h2>
					<p>Your Account:</p>
					<p>Email: ".$email."</p>
					<p>Please click the link below to reset your password.</p>
					<a href='http://localhost:8081/files/password_reset.php?code=".$code."&user=".$row['id']."'>Reset Password</a>
				";

                require '../../vendor/autoload.php';

                $mail = new PHPMailer(true);
                try
                {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'favourakak@gmail.com';
                    $mail->Password = '09035222902';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('favourakak@gmail.com', 'Annibel Jewelry & Collections');
                    $mail->addAddress($email, $row['fullname']);
                    $mail->addReplyTo('favourakak@gmail.com', 'For any Information');
                    $mail->addCC('favourakak@gmail.com');

                    $mail->isHTML(true);
                    $mail->Subject = 'Annibel Jewelry & Collections Password Reset';
                    $mail->Body    = $message;
                    $mail->AltBody = "Thank you for Using Annibel Jewelry & Collections";

                    $mail->send();

                    return $result = ['response' => true, 'message' => 'Password reset link sent to your Registered Email'];
                }
                catch (Exception $eax) 
                {
                    return $result = ['response' => false, 'message' => 'EMAIL SENDING FAILED. INFO: '.$mailer->ErrorInfo];
                }
            }
            else
            {
                return $result = ['response' => false, 'message' => 'Email not Found'];
            }
        }
		catch(PDOException $ex)
		{
			return $result = ['response' => false, 'message' => 'Error connecting to Server: '.$ex];
		}
		finally
		{
			$this->connection()->close();
		}
    }

    function sendMailtoUser($message, $email)
    {
        $message = "
                <h3>Thank you for Ordering on Annibel Jewelry & Collections.</h3>
                <p>Your Order Report</p>
                
                <p>And don't forget to order again</p>
            ";

            //Load Composer's autoloader
            require '../../vendor/autoload.php';

            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);
            try
            {
                $mail->isSMTP();
                //Set the SMTP server to send through
                $mail->Host = 'smtp.gmail.com';
                //Enable SMTP authentication
                $mail->SMTPAuth = true;
                $mail->Username = 'favourakak@gmail.com';
                //SMTP password
                $mail->Password = '09035222902';
                //Enable implicit TLS encryption
                $mail->SMTPSecure = 'tls';
                //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                $mail->Port = 587;

                //Recipients
                $mail->setFrom('favourakak@gmail.com', 'Annibel Jewelry & Collections');
                //Add a recipient
                $mail->addAddress($email, $username);
                //Name is optional
                //$mail->addAddress('favourakak@gmail.com');
                $mail->addReplyTo('favourakak@gmail.com', 'For any Information');
                $mail->addCC('favourakak@gmail.com');

                //Content
                //Set email format to HTML
                $mail->isHTML(true);
                $mail->Subject = 'Thank you for Ordering on Annibel Jewelry & Collections';
                $mail->Body    = $message;
                $mail->AltBody = "Thank you for Ordering on Annibel Jewelry & Collections";

                $mail->send();

                $result = ['response' => true, 'message' => 'Payment made successfully'];
            }
            catch (Exception $eax) 
            {
                return $result = ['response' => false, 'message' => 'EMAIL SENDING FAILED. INFO: '.$mail->ErrorInfo];
            }
    }

    //delete
	public function deleteUser($idds)
	{
		$db_handle = $this->connection()->open();
		try
		{
			$stmt= $db_handle->prepare("DELETE FROM users WHERE id=?");
			$stmt->execute([$this->SharedComponents()->unprotect($idds)]);

			$result = ['response' => true, 'message' => 'User Account Deleted successfully'];
			return $result;
		}
		catch(PDOException $e)
		{
			$result = ['response' => false, 'message' => 'Error Connecting to Server '.$ex];
			return $result;
		}
		finally
		{
			$this->connection()->close();
		}
    }

}
?>