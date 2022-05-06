<?php
  include 'includes/header.php';
  if(!isset($_GET['postid']) || !isset($_GET['userid']))
  {
    header("Location: ./");
  }
  include 'includes/navbar.php';
?>

  <!-- Subheader Start -->
  <div class="subheader bg-cover dark-overlay dark-overlay-2" style="background-image: url('assets/img/subheader.jpg')">
    <div class="container">
      <div class="subheader-inner">
        <h1>Edit Blog Post</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
            <?php
                include_once 'includes/connect.php';
                $userid = $_GET['userid'];
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
      <?php
          include_once 'includes/connect.php';
          $postid = $_GET['postid'];
          $sql = "SELECT * FROM blog WHERE Id='$postid'";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) 
          {
            $title;
            $categoryid;
            $content;
            $description;
            $image;
            // output data of each row
            while($row = $result->fetch_assoc()) 
            {
              $title = $row["Title"];
              $categoryid = $row["CategoryId"];
              $content = $row["Content"];
              $description = $row["Description"];
              $image = $row["Image"];
            }

            $esql = "SELECT * FROM category WHERE Id = '$categoryid'";
            $eresult = $conn->query($esql);
            if ($eresult->num_rows > 0) 
            {
              // output data of each row
              while($erow = $eresult->fetch_assoc()) 
              {
                $category = $erow["Title"];
              }
            }
      ?>
       <form method="POST" action="includes/allcodes.php" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-12">
            <h5>Edit Blog Post</h5>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 form-group">
            <input type="text" class="form-control" placeholder="Title" name="title" value="<?php echo $title; ?>">
          </div>
          <div class="col-md-6 form-group">
              <input name="categoryid" type="text" class="form-control" list="category" placeholder="Product Category" value="<?php echo $category; ?>">
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
            <textarea class="form-control" placeholder="Content" name="content"><?php echo $content; ?></textarea>
          </div>
          <div class="col-md-12 form-group">
            <textarea type="text" class="form-control" placeholder="Description" name="description" rows="7"><?php echo $description; ?></textarea>
          </div>
          <div class="col-md-6 form-group">
            <img src="uploads/<?php echo $image; ?>" alt="" style="width: 100px;height:100px" class="p-2">
            <p>Image</p>
            <input type="file" class="form-control" name="image" id="image" rows="7">
          </div>
          <input type="hidden" name="postid" value="<?php echo $postid ?>">
          <input type="hidden" name="userid" value="<?php echo $userid ?>">
          <div class="col-md-6 form-group">
            <button style="float:right" type="submit" class="btn-custom btn-sm danger" name="editpost">Post</button>
          </div>
        </div>
      </form>
      <?php
          }
          //$conn->close();
      ?>
    </div>
  </div>
  <!-- Dashboard Post End -->


<?php
  include 'includes/footer.php';
  include 'includes/scripts.php';
?>
