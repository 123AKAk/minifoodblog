<?php
  include 'includes/header.php';
  
  if(isset($_SESSION['admin']))
  {
    header("Location: admin.php");
  }
  else if(!isset($_SESSION['user']))
  {
    header("Location: ./");
  }
  include 'includes/navbar.php';
?>

  <!-- Subheader Start -->
  <div class="subheader bg-cover dark-overlay dark-overlay-2" style="background-image: url('assets/img/subheader.jpg')">
    <div class="container">
      <div class="subheader-inner">
        <h1>User Dashboard</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="./">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">
            <?php
                include_once 'includes/connect.php';
                $userid = $_SESSION['user'];
                $sql = "SELECT * FROM user WHERE Id='$userid'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) 
                {
                  // output data of each row
                  while($row = $result->fetch_assoc()) 
                  {
                    echo $row["Firstname"];
                  }
                }
                //$conn->close();
            ?>
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
  <!-- Subheader End -->

  <!-- Dashboard Post Start -->
  <div class="section section-padding single-post-1">
    <div class="container">
    <?php
        include 'includes/feedback.php';
    ?>
    </div>
    <div class="container">
      <form method="POST" action="includes/allcodes.php" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-12">
          <a style="float:right" href="profile.php?id=<?php echo $_SESSION['user'] ?>" class="btn-custom btn-sm">Edit Account</a>
          </div>
          <div class="col-md-12">
            <h5>Create new Blog Post</h5>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group">
            <input type="text" class="form-control" placeholder="Title" name="title" value="">
          </div>
          <div class="col-md-6 form-group">
              <input name="categoryid" type="text" class="form-control" list="category" placeholder="Product Category">
              <datalist id="category">
                <?php
                    $sql = "SELECT * FROM category";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) 
                    {
                      // output data of each row
                      while($row = $result->fetch_assoc()) 
                      {
                ?>
                    <option value="<?php echo $row["Title"]; ?>"></option>
                <?php
                      }
                    }
                    //$conn->close();
                ?>
              </datalist>
          </div>
          <div class="col-md-12 form-group">
            <textarea class="form-control" placeholder="Content" name="content" value=""></textarea>
          </div>
          <div class="col-md-12 form-group">
            <textarea type="text" class="form-control" placeholder="Description" name="description" value="" rows="7"></textarea>
          </div>
          <div class="col-md-6 form-group">
            <p>Image</p>
            <input type="file" class="form-control" name="image" id="image" rows="7">
          </div>
          <input type="hidden" name="location" value="dashboard.php">
          <input type="hidden" name="userid" value="<?php echo $_SESSION['user'] ?>">
          <div class="col-md-6 form-group">
            <button style="float:right" type="submit" class="btn-custom btn-sm danger" name="submitpost">Post</button>
          </div>
        </div>
      </form>

      <br>
      <br>
      <div class="row table-responsive ">
        <h5>All Blog Posts</h5>
        <!-- table start-->
        <table class="table table-striped">
          <thead class="thead-light">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Image</th>
              <th scope="col">First Name</th>
              <th scope="col">Title</th>
              <th scope="col">Category</th>
              <th scope="col">Content</th>
              <th scope="col">Date</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
                $sql = "SELECT * FROM blog WHERE UserId = '$userid'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) 
                {
                  $num = 0;
                  // output data of each row
                  while($row = $result->fetch_assoc()) 
                  {
                    $num ++;
            ?>
              <tr>
                <td><?php echo $num; ?></td>
                <td><img src="uploads/<?php echo $row["Image"]; ?>" alt="" ></td>
                <td>
                  <?php
                    $userid = $row["UserId"];
                    $asql = "SELECT * FROM user WHERE Id='$userid'";
                    $aresult = $conn->query($asql);
                    if ($aresult->num_rows > 0) 
                    {
                      // output data of each row
                      while($arow = $aresult->fetch_assoc()) 
                      {
                        echo $arow["Firstname"];
                      }
                    }
                  ?>
                </td>
                <td><?php echo $row["Title"]; ?></td>
                <td>
                  <?php
                    $catid = $row["CategoryId"];
                    $bsql = "SELECT * FROM category WHERE Id='$catid'";
                    $bresult = $conn->query($bsql);
                    if ($bresult->num_rows > 0) 
                    {
                      // output data of each row
                      while($brow = $bresult->fetch_assoc()) 
                      {
                        echo $brow["Title"];
                      }
                    }
                  ?>
                </td>
                <td><?php echo $row["Content"]; ?></td>
                <td><?php echo $row["Date"]; ?></td>
                <td>
                  <a href="editpost.php?postid=<?php echo $row["Id"]; ?>&userid=<?php echo $userid; ?>" class="btn btn-primary">
                    Edit
                  </a>
                  <a href="includes/allcodes.php?deletepostid=<?php echo $row["Id"]; ?>&pagename=../dashboard.php" class="btn btn-danger">
                    Delete
                  </a>
                </td>
              </tr>
            <?php
                  }
                }
                //$conn->close();
            ?>
            
          </tbody>
        </table>
        <!-- table end -->
      </div>

    </div>
  </div>
  <!-- Dashboard Post End -->


<?php
  include 'includes/footer.php';
  include 'includes/scripts.php';
?>
