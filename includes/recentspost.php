    <div class="col-lg-4">
        <div class="sidebar">

        <div class="sidebar-widget">
            <h5>Recent Posts</h5>

            <?php
                $sql = "SELECT * FROM blog ORDER BY Id DESC LIMIT 4";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) 
                {
                  // output data of each row
                  while($row = $result->fetch_assoc()) 
                  {
                    $sales_date = new DateTime($row["Date"]);
                    $date = $sales_date->format('d')." ".$sales_date->format('M')."' ". $sales_date->format('y');
            ?>
                <article class="media">
                    <a href="single.php?productid=<?php echo $row["Id"]; ?>">
                        <img style="width:100%;height: 100px;" src="uploads/<?php echo $row["Image"]; ?>">
                    </a>
                    <div class="media-body">
                        <h6>
                            <a href="single.php?productid=<?php echo $row["Id"]; ?>">
                            <?php echo $row["Title"]; ?>
                            </a>
                        </h6>
                        <p><?php echo $date; ?></p>
                    </div>
                </article>
            <?php
                  }
                }
                //$conn->close();
            ?>
        </div>
        
        <div class="sidebar-widget tags">
            <h5>Category(s)</h5>
            <?php
                $sql = "SELECT * FROM category";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) 
                {
                    // output data of each row
                    while($row = $result->fetch_assoc()) 
                    {
            ?>
                <a href="category.php?catid=<?php echo $row["Id"]; ?>"><?php echo $row["Title"]; ?></a>
            <?php
                    }
                }
                //$conn->close();
            ?>
        </div>

        </div>
    </div>