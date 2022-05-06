
  <!-- Preloader start -->
  <!-- <div class="ct-preloader">
    <div class="ct-preloader-inner">
      <div class="ct-preloader-pan">
        <div class="ct-preloader-pan-inner">
          <div class="ct-preloader-pan-circle"></div>
          <div class="ct-preloader-pan-handle"></div>
        </div>
        <div class="ct-preloader-pancake">
          <div class="ct-preloader-pancake-inner"></div>
        </div>
      </div>
    </div>
  </div> -->
  <!-- Preloader End -->

  <!-- Aside (Mobile Navigation) -->
  <aside class="main-aside">
    <div class="aside-scroll">
      <ul>
        <li class="menu-item">
          <a href="./">Home</a>
        </li>
        <li class="menu-item">
          <a href="login.php">Login</a>
        </li>
        <li class="menu-item">
          <a href="register.php">Register</a>
        </li>
        <li class="menu-item">
            <a href="dashboard.php">Dashboard</a>
        </li>
      </ul>
    </div>
  </aside>
  <div class="aside-overlay aside-trigger"></div>

  <!-- Header Start -->
  <header class="main-header header-1">
    <nav class="navbar">
      <div class="container">
        <!-- Menu -->
        <ul class="navbar-nav">
            <li class="menu-item">
                <a href="./">Home</a>
            </li>
            <li class="menu-item">
                <a href="login.php">Login</a>
            </li>
            <li class="menu-item">
                <a href="register.php">Register</a>
            </li>
            <?php
              if(isset($_SESSION['admin']) || isset($_SESSION['user']))
              {
            ?>
               <li class="menu-item">
                <a href="dashboard.php">Dashboard</a>
              </li>
            <?php
              }
            ?>
            <?php
              if(isset($_SESSION['admin']) || isset($_SESSION['user']))
              {
            ?>
              <li class="menu-item">
                <a href="logout.php">Logout</a>
              </li>
            <?php
              }
            ?>
        </ul>
        <div class="header-controls">
          <!-- Toggler -->
          <div class="aside-toggler aside-trigger">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
      </div>
    </nav>
  </header>
  <!-- Header End -->
