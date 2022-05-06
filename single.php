<?php
  include 'includes/header.php';
  if(!isset($_GET['productid']))
  {
    header("Location: ./");
  }
  include 'includes/navbar.php';
?>

  <!-- Subheader Start -->
  <div class="subheader bg-cover dark-overlay dark-overlay-2" style="background-image: url('assets/img/subheader.jpg')">
    <div class="container">
      <div class="subheader-inner">
        <h1>Blog Post</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="./">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">
              <?php
                include_once 'includes/connect.php';
                $postid = $_GET['productid'];
                $esql = "SELECT * FROM blog WHERE Id = '$postid'";
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
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
  <!-- Subheader End -->

  <!-- Blog Post Start -->
  <div class="section section-padding single-post-1">
    <div class="container">

      <div class="row">
        <div class="col-lg-8">

          <?php
            include_once 'includes/connect.php';
            $postid = $_GET['productid'];
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
                $userid = $row["UserId"];
                $categoryid = $row["CategoryId"];
                $content = $row["Content"];
                $description = $row["Description"];
                $image = $row["Image"];

                $sales_date = new DateTime($row["Date"]);
                $date = $sales_date->format('d')." ".$sales_date->format('M')."' ". $sales_date->format('y');
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
          <!-- Content Start -->
          <article class="post-single">
            <div class="post-thumbnail">
              <img src="uploads/<?php echo $image; ?>" alt="post">
            </div>
            <div class="post-categories">
              <a href="category.php?catid=<?php echo $categoryid; ?>">
              <?php echo $category; ?></a>
            </div>
            <h2 class="title"><?php echo $title; ?></h2>
            <div class="post-meta">
              <span> <i class="far fa-calendar"></i><?php echo $date; ?></span>
              <span> <i class="far fa-user"></i>
                Posted by
                <?php
                  $userid = $userid;
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
            <div class="post-content">
              <blockquote>
                <p><?php echo $content; ?></p>
              </blockquote>
              <p>
                <?php echo $description; ?>
              </p>
            </div>
          </article>
          <!-- Content End -->
          <?php
              }
              //$conn->close();
          ?>

          <!-- Comments Start -->
          <?php
              include 'includes/feedback.php';
          ?>
          <?php
                $ssql = "SELECT * FROM comment ORDER BY Id DESC";
                $sresult = $conn->query($ssql);
                if ($sresult->num_rows > 0) 
                {
          ?>
          <div class="comments-list">
            <h4><?php echo $rowcount = mysqli_num_rows( $result );?> Comment(s)</h4>
            <ul>
            <?php
              // output data of each row
              while($srow = $sresult->fetch_assoc()) 
              {
                $ssales_date = new DateTime($srow["Date"]);
                $sdate = $ssales_date->format('d')." ".$ssales_date->format('M')."' ". $ssales_date->format('y');
            ?>
              <li class="comment-item">
                <img src="assets/img/people/people-1.jpg" alt="comment author">
                <div class="comment-body">
                  <h5>
                    <?php
                      $duserid = $srow["UserId"];
                      $dsql = "SELECT * FROM user WHERE Id='$duserid'";
                      $dresult = $conn->query($dsql);
                      if ($dresult->num_rows > 0) 
                      {
                        // output data of each row
                        while($drow = $dresult->fetch_assoc()) 
                        {
                          echo $drow["Firstname"];
                        }
                      }
                    ?>
                  </h5>
                  <span>Posted on: <?php echo $sdate; ?></span>
                  <p>
                    <?php echo $srow["Text"] ?>
                  </p>
                </div>
              </li>
            </ul>
          </div>
          <?php
              }
            }
            else
            {
              echo "<h5>No Comments</h5>";
            }
            //$conn->close();
          ?>

          <div class="comment-form">
            <h4>Leave a Reply</h4>
            <form method="POST" action="includes/allcodes.php">
              <div class="row">
                <div class="col-md-12 form-group">
                  <input type="hidden" name="postid" value="<?php echo $postid ?>">
                  <textarea class="form-control" placeholder="Type your comment..." name="comment" rows="7"></textarea>
                </div>
              </div>
              <button type="submit" class="btn-custom primary" name="commentsubmit">Post comment</button>
            </form>
          </div>
          <!-- Comments End -->

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
