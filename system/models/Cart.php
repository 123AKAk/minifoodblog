<?php

class Carting
{
	function connection(){return new Database();}
	
	function SharedComponents(){return new SharedComponents();}

	function addCart($idds)
	{
		session_start();
		try
		{
			$decryptedId = $this->SharedComponents()->unprotect($idds);

			$db_handle = $this->connection()->open();

			$astmt = $db_handle->prepare("SELECT * FROM products WHERE id=:id");
			$astmt->execute(['id' => $decryptedId]);
			$productByCode = $astmt->fetchAll(PDO::FETCH_ASSOC);
			$itemArray = array($productByCode[0]["id"]=>array('id'=>$productByCode[0]["id"], 'quantity'=>1));
			
			if(!empty($_SESSION["cart_item"])) 
			{
				$a = [];
				foreach ($_SESSION['cart_item'] as $result)
				{
					array_push($a, $result["id"]);
				}

				if(in_array($decryptedId, $a))
				{
					$result = ['response' => true, 'message' => 'Product Already in Cart'];
				}
				else
				{
					$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);                        
					$result = ['response' => true, 'message' => 'Added to Cart'];
				}
			}
			else
			{
				$_SESSION["cart_item"] = $itemArray;
				$result = ['response' => true, 'message' => 'Added to Cart'];
			}
		}
		catch(PDOException $ex)
		{
			$result = ['response' => false, 'message' => 'Error Connecting to Server '.$ex];
		}
		finally
		{
			$this->connection()->close();
		}

		session_write_close();
		return $result;
	}
	
	function deleteCart($idds)
	{
		session_start();
		$decryptedId = $this->SharedComponents()->unprotect($idds);

		if(!empty($_SESSION["cart_item"])) 
		{
			foreach($_SESSION["cart_item"] as $k => $v) 
			{
				if($decryptedId == $v["id"])
				{
					unset($_SESSION["cart_item"][$k]);
					if(empty($_SESSION["cart_item"]))
					{
						unset($_SESSION["cart_item"]);
						$result = ['response' => true, 'message' => 'Cart Emptied'];
					}
					else
					{
						$result = ['response' => true, 'message' => 'Product Removed from Cart'];
					}
				}
			}
		}
		session_write_close();
		return $result;
	}

	function getSession()
	{
		session_start();
		if(!empty($_SESSION["cart_item"]))
		{
			$result = ['response' => true, 'message' => count($_SESSION["cart_item"])];			
		}
		else
		{
			$result = ['response' => true, 'message' => 0];			
		}
		session_write_close();
		return $result;
	}

	function updateCartAdd($idds)
	{
		session_start();
		$decryptedId = $this->SharedComponents()->unprotect($idds);

		if(!empty($_SESSION["cart_item"])) 
		{
			foreach($_SESSION["cart_item"] as $k => $v) 
			{
				if($decryptedId == $v["id"])
				{
					$_SESSION["cart_item"][$k]["quantity"] += 1;

					// return $result = ['response' => true, 'message' => 'Cart Updated'];
					session_write_close();
					return $result = ['response' => true, 'message' => ''];
				}
				if(empty($_SESSION["cart_item"]))
				{
					// return $result = ['response' => true, 'message' => 'Cart Emptied'];
				}
			}
		}
		else
		{
			// return $result = ['response' => false, 'message' => 'Null'];
		}
	}

	function updateCartMinus($idds)
	{
		session_start();
		$decryptedId = $this->SharedComponents()->unprotect($idds);

		if(!empty($_SESSION["cart_item"])) 
		{
			foreach($_SESSION["cart_item"] as $k => $v) 
			{
				if($decryptedId == $v["id"])
				{
					if($_SESSION["cart_item"][$k]["quantity"] >= 1)
					{
						$_SESSION["cart_item"][$k]["quantity"] -= 1;
					}

					// $result = ['response' => true, 'message' => 'Cart Updated'];
					session_write_close();
					return $result = ['response' => true, 'message' => ''];
				}
				if(empty($_SESSION["cart_item"]))
				{
					unset($_SESSION["cart_item"]);

					// $result = ['response' => true, 'message' => 'Cart Emptied'];
				}
			}
		}
		else
		{
			// $result = ['response' => false, 'message' => 'Null'];
		}
	}

	function emptyCart()
	{
		session_start();
		unset($_SESSION["cart_item"]);
		session_write_close();
		return $result = ['response' => true, 'message' => 'Cart Emptied'];
	}
}
?>