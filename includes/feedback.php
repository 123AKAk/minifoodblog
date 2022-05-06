<div>
    <?php
        if(!empty($_SESSION['success']))
        {
    ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
        <center><strong>Success! </strong><?php echo $_SESSION['success'] ?>.</center>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
    <?php
        $_SESSION['success'] = "";
        }
        else if(!empty($_SESSION['failed']))
        {
    ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <center><strong>Failure! </strong><?php echo $_SESSION['failed'] ?>.</center>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
    <?php
        $_SESSION['failed'] = "";
        }
    ?>
</div>