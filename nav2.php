<header id="header" class="header fixed-top d-flex align-items-center" style="background-color: #57737A;">

    <div class="d-flex align-items-center justify-content-between" style="background-color: #57737A;">
      <a href="usermain.php" class="logo d-flex align-items-center">
        <img src="crimelogo.png" alt="" style="border-radius: 50%;">
        <span class="d-none d-lg-block" style="color: #ffffff;">GuardianWatch</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn" style="color: white;" ></i>
    </div><!-- End Logo -->

    

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        

        
      

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="upload/<?php
                     
                        $id =  $_SESSION['id'];
                        $sql = "select * from users where id = '$id'";
                          $result = mysqli_query($conn, $sql);
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo $row['image'];
                                        }
                        ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php
                     
                        $id =  $_SESSION['id'];
                        $sql = "SELECT CONCAT(first_name, ' ', middle_name, ' ', last_name) AS full_name FROM users WHERE id = '$id'";
                          $result = mysqli_query($conn, $sql);
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo $row['full_name'];
                                        }
                        ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php
                     
                        $id =  $_SESSION['id'];
                        $sql = "SELECT CONCAT(first_name, ' ', middle_name, ' ', last_name) AS full_name FROM users WHERE id = '$id'";
                          $result = mysqli_query($conn, $sql);
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo $row['full_name'];
                                        }
                        ?></h6>
              <span>Users</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

           

            
          

            
          

            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <aside id="sidebar" class="sidebar" style="background-color:#add8e6;color: white;">

    <ul class="sidebar-nav" id="sidebar-nav" >

      <li class="nav-item" >
        <a class="nav-link collapsed" href="usermain.php" style="background-color:#add8e6;color: white;">
          <i class="bi bi-grid" style="color: white;"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->
     
      <li class="nav-item">
        <a class="nav-link collapsed" href="" style="background-color:#add8e6;color: white;">
             <i class="bi bi-person-lines-fill" style="color: white;"></i>
          <span>Reports</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="" style="background-color:#add8e6;color: white;">
                      <i class="bi bi-people-fill" style="color: white;"></i>

          <span>On-Going</span>
        </a>
      </li>
     
     

      <li class="nav-item">
        <a class="nav-link collapsed" href="" style="background-color:#add8e6;color: white;">
            <i class="bi bi-card-list" style="color: white;"></i>
          <span>Completed</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="" style="background-color:#add8e6;color: white;">
          <i class="bi bi-map" style="color: white;"></i>
          <span>Map</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="" style="background-color:#add8e6;color: white;">
          <i class="bi bi-map" style="color: white;"></i>
          <span>Notification</span>
        </a>
      </li>
     
     

      

      


     

      

      

    </ul>

  </aside><!-- End Sidebar-->