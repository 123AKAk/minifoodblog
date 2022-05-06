<?php
require_once('../database/conn.php');
require_once('../models/userSharedComponents.php');

$namespace = $_POST['namespace'];

if($namespace != '')
{
    if($namespace == "addCart") 
    {
        require '../models/Cart.php';
        $model = new Carting();
        
        $result = $model->addCart($_POST['id']);
        echo json_encode($result);
    }
    else if($namespace == "deleteCart")
    {
        require '../models/Cart.php';
        $model = new Carting();
        
        $result = $model->deleteCart($_POST['id']);
        echo json_encode($result);
    }
    else if($namespace == "getSession")
    {
        require '../models/Cart.php';
        $model = new Carting();
        
        $result = $model->getSession();
        echo json_encode($result);
    }
    else if($namespace == "updateCartAdd")
    {
        require '../models/Cart.php';
        $model = new Carting();
        
        $result = $model->updateCartAdd($_POST['id']);
        echo json_encode($result);
    }
    else if($namespace == "updateCartMinus")
    {
        require '../models/Cart.php';
        $model = new Carting();
        
        $result = $model->updateCartMinus($_POST['id']);
        echo json_encode($result);
    }
    else if($namespace == "emptyCart")
    {
        require '../models/Cart.php';
        $model = new Carting();
        
        $result = $model->emptyCart();
        echo json_encode($result);
    }
    else if($namespace == "updatebillingdetails")
    {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
		$address = $_POST['address'];
		$town_city = $_POST['town_city'];
		$country = $_POST['country'];
		$zipcode = $_POST['zipcode'];
		$contact_info = $_POST['contact_info'];
        $ordernote = $_POST['ordernote'];

        require '../models/userAccountModel.php';
        $model = new Users();
        
        $result = $model->updateUserAccount($_POST['id'], $fullname, $email,  $address, $town_city, $country, $zipcode, $contact_info, $ordernote);
        echo json_encode($result);
    }
    else if($namespace == "changePassword")
    {
        $password = $_POST['password'];

        require '../models/userAccountModel.php';
        $model = new Users();
        
        $result = $model->changePassword($_POST['id'], $password);
        echo json_encode($result);
    }
    else if($namespace == "addbillingdetails")
    {
		$email = $_POST['email'];
		$password = $_POST['password'];
        $fullname = $_POST['fullname'];
		$address = $_POST['address'];
		$town_city = $_POST['town_city'];
		$country = $_POST['country'];
		$zipcode = $_POST['zipcode'];
		$contact_info = $_POST['contact_info'];
        $ordernote = $_POST['ordernote'];

        require '../models/userAccountModel.php';
        $model = new Users();
        
        $result = $model->registerUserAccount_cart($email, $password, $fullname, $address, $town_city, $country, $zipcode, $contact_info, $ordernote);
        echo json_encode($result);
    }
    else if($namespace == "register")
    {
        $username = $_POST['username'];
		$email = $_POST['email'];
		$password = $_POST['password'];

        require '../models/userAccountModel.php';
        $model = new Users();
        
        $result = $model->registerUserAccount($username, $email, $password);
        echo json_encode($result);
    }
    else if($namespace == "login")
    {
		$email = $_POST['email'];
		$password = $_POST['password'];

        require '../models/userAccountModel.php'; 
        $model = new Users();
        
        $result = $model->verifyUser($email, $password);
        echo json_encode($result);
    }
    else if($namespace == "passwordreset")
    {
		$email = $_POST['email'];

        require '../models/userAccountModel.php';
        $model = new Users();
        
        $result = $model->passwordreset($email);
        echo json_encode($result);
    }
    else if($namespace == "runSales")
    {
		$user_id = $_POST['user_id'];
        $transaction_id = $_POST['transaction_id'];
        $amount = $_POST['amount'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        require '../models/userAccountModel.php';
        $model = new Users();
        
        $result = $model->runSales($user_id, $transaction_id, $amount, $product_id, $quantity);
        echo json_encode($result);
    }
    else
    {
        $result =  ['response' => false, 'message' => 'Prevented: Adultrated Request Received!'.$ex];
        echo json_encode($result);
    }
}
else
{
    $result =  ['response' => false, 'message' => 'Prevented: Adultrated Request Received!'.$ex];
    echo json_encode($result);
}


?>