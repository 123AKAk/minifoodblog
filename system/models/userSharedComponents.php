<?php

class SharedComponents
{
	function connection(){return new Database();}

	//get anything based on query
	function getData($query)
	{
		$db_handle = $this->connection()->open();
		try
		{
			$stmnt = $db_handle->query($query);
			$data = $stmnt->fetchAll(PDO::FETCH_ASSOC);
			return $data;
		}
		catch(PDOException $ex){}
		finally
		{
			$this->connection()->close();
		}
	}

	//get anything by id based on query
	function getDataById($idds, $query)
    {
		$db_handle = $this->connection()->open();
		try
		{
			$astmt = $db_handle->prepare($query);
			$astmt->execute(['id' => $this->unprotect($idds)]);
			$item = $astmt->fetch();

			return $item;
		}
		catch(PDOException $ex){}
		finally
		{
			$this->connection()->close();
		}
    }

	//get category name
    function getCategoryName($id)
	{
        $db_handle = $this->connection()->open();
		try
		{
			$astmt = $db_handle->prepare("SELECT catname FROM category WHERE id=:id");
			$astmt->execute(['id' => $id]);
			$catname = $astmt->fetch();

            if($catname)
            {
                return $catname;
            }
			return false;
		}
		catch(PDOException $ex)
		{
			return false;
		}
		finally
		{
			$this->connection()->close();
		}
    }

	//get category
	function getCategory()
	{
		$db_handle = $this->connection()->open();
        try
		{
            $statement = $db_handle->query("SELECT * FROM category");
            $query = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $query;
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

	//encrypt the datastring
	function protect($routeValue)
	{
		// Store a string into the variable which
		// need to be Encrypted
		$data = $routeValue."";

		// Store the cipher method
		$ciphering = "AES-128-CTR";

		// Use OpenSSl Encryption method
		$iv_length = openssl_cipher_iv_length($ciphering);
		$options = 0;

		// Non-NULL Initialization Vector for encryption
		$encryption_iv = '1234567891011121';

		// Store the encryption key
		$encryption_key = "eyo123";

		// Use openssl_encrypt() function to encrypt the data
		$encryption = openssl_encrypt($data, $ciphering,
		$encryption_key, $options, $encryption_iv);

		return $encryption;
	}

	//decrypt the datastring
	function unprotect($encryptedValue)
	{
		$ciphering = "AES-128-CTR";

		// Use OpenSSl Encryption method
		$iv_length = openssl_cipher_iv_length($ciphering);
		$options = 0;

		// Non-NULL Initialization Vector for decryption
		$decryption_iv = '1234567891011121';

		// Store the decryption key
		$decryption_key = "eyo123";

		// Use openssl_decrypt() function to decrypt the data
		$decryption = openssl_decrypt ($encryptedValue, $ciphering, 
		$decryption_key, $options, $decryption_iv);

		return $decryption;
	}

	//gets the total number of items in the table
	function getItemCount($tableName)
	{
		$db_handle = $this->connection()->open();
		try
		{
			$stm = $db_handle->prepare("SELECT * FROM $tableName");
			$stm->execute();
			$count = $stm->rowCount();
			return $count;
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

	//gets total number of items by category name
	function getItemCountByCategory($idds)
	{
		$db_handle = $this->connection()->open();
		try
		{
			$astmt = $db_handle->prepare("SELECT * FROM products WHERE category_id=:category_id");
        	$astmt->execute(['category_id' => $this->unprotect($idds)]);
			$count = $astmt->rowCount();

			return $count;
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
	//[Carried from admin sharedcomponents]
	//get cart items by userid
	function getCartByUserId($idds)
    {
        $db_handle = $this->connection()->open();

        $astmt = $db_handle->prepare("SELECT * FROM cart WHERE userid=:userid ORDER BY id DESC");
        $astmt->execute(['userid' => $this->unprotect($idds)]);
        $item = $astmt->fetchAll(PDO::FETCH_ASSOC);

        return $item;
    }
}
?>