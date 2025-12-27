<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<?php include "includes/header.php"; ?>

    <!------------------------------ account-page details------------------------------>

    <div class="account-page">
      <div class="container">
        <div class="row">
          <div class="col-2">
            <img src="assets/image1.png" width="100%" />
          </div>
          <div class="col-2">
            <div class="form-container">
              <div class="form-btn">
                <span onclick="login()">Login</span>
                <span onclick="register()">Register</span>
                <hr id="Indicator" />
              </div>
              <form id="LoginForm" method="POST" action="auth/login.php">
                <input type="text" name="username" placeholder="username" required  />
                <input type="password" name="password" placeholder="Password" required />
                <button type="submit" class="btn">Login</button>
                <a href="">Forgot password</a>
              </form>

              <form id="RegForm" method="POST" action="auth/register.php">
                <input type="text" name="username" placeholder="Username" required />
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <button type="submit" class="btn">Register</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <!-----------------------------------js for toggle form-------------------------------------->
    <script>
      var LoginForm = document.getElementById("LoginForm");
      var RegForm = document.getElementById("RegForm");
      var Indicator = document.getElementById("Indicator");

      function register() {
        RegForm.style.transform = "translateX(0px)";
        LoginForm.style.transform = "translateX(0px)";
        Indicator.style.transform = "translateX(100px)";
      }
      function login() {
        RegForm.style.transform = "translateX(300px)";
        LoginForm.style.transform = "translateX(300px)";
        Indicator.style.transform = "translateX(0px)";
      }
    </script>
  
<?php include "includes/footer.php"; ?>
