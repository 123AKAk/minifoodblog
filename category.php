<?php
  include 'includes/header.php';
  if(!isset($_GET['catid']))
  {
    header("Location: ./");
  }
  include 'includes/navbar.php';
?>

  <!-- Subheader Start -->
  <div class="subheader bg-cover dark-overlay dark-overlay-2" style="background-image: url('assets/img/subheader.jpg')">
    <div class="container">
      <div class="subheader-inner">
        <h1>
          <?php
            include_once 'includes/connect.php';
            $prodcatid = $_GET['catid'];
            $esql = "SELECT * FROM category WHERE Id = '$prodcatid'";
            $eresult = $conn->query($esql);
            if ($eresult->num_rows > 0) 
            {
              // output data of each row
              while($erow = $eresult->fetch_assoc()) 
              {
                $category = $erow["Title"];
              }
              echo $category;
            }
          ?>
        </h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="./">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">
            Category | <?php echo $category; ?>
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
  <!-- Subheader End -->

  <!-- Blog Posts Start -->
  <div class="section section-padding posts">
    <div class="container">

      <div class="row">
        <div class="col-lg-8">

        <div class="row">

          <?php
              $sql = "SELECT * FROM blog WHERE CategoryId='$prodcatid' ORDER BY Id DESC";
              $result = $conn->query($sql);
              if ($result->num_rows > 0) 
              {
                // output data of each row
                while($row = $result->fetch_assoc()) 
                {
                  $sales_date = new DateTime($row["Date"]);
                  $date = $sales_date->format('d')." ".$sales_date->format('M')."' ". $sales_date->format('y');
          ?>
          <!-- Post Start -->
          <div class="col-lg-6 col-md-6">
            <article class="post">
              <div class="post-thumbnail">
                <a href="single.php?productid=<?php echo $row["Id"]; ?>" ><img style="width:100%;height: 290px;" src="uploads/<?php echo $row["Image"]; ?>" alt="blog post"></a>
                <div class="post-meta">
                  <span><?php echo $date; ?></span>
                  <span>
                    Posted by
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
                  </span>
                </div>
              </div>
              <div class="post-categories">
                  <a href="category.php?catid=<?php echo $row["CategoryId"]; ?>">
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
                  </a>
              </div>
              <div class="post-body">
                <h5 class="post-title">
                  <a href="single.php?productid=<?php echo $row["Id"]; ?>">
                    <?php echo $row["Title"]; ?>
                  </a>
                </h5>
                <p class="post-text">
                  <?php echo $row["Content"]; ?>
                </p>
              </div>
            </article>
          </div>
          <!-- Post End -->
          <?php
                }
              }
              else
              {
                echo "<br><h4>Sorry, Category does not contain a Post</h4>";
              }
              //$conn->close();
          ?>
          </div>

        </div>
        <?php
          include 'includes/recentspost.php';
        ?>
      </div>

    </div>
  </div>
  <!-- Blog Posts End -->

<?php
  include 'includes/footer.php';
  include 'includes/scripts.php';
?>
