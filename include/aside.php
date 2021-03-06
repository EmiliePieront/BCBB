<?php 
/*Récupére les infos utilisateurs*/ 
if (isset($_POST['validateone'])){
   /*----------Récupère le username-----------*/
    /*vide === empty*/ 
    if (!empty($_POST['userName']) AND !empty($_POST['pass'])){
        $userName = htmlspecialchars($_POST['userName']);
        /*htmlspecialchars = permet de sécuriser*/ 
        $pass = hashPwd($_POST['pass']);
        // $pass = sha1($_POST['pass']);
        $check_presence_user = $bdd->prepare('SELECT * FROM users WHERE username = ? AND pwd = ?');
        /* "*" veut dire tous les champs*/ 
        /*Permet de vérifier si l'utilisateur existe dans la base de donnée*/ 
        /* "*" permet de trouver tout ce qui concorde avec la demande*/ 
        $check_presence_user->execute(array($userName, $pass));
        /*permet de vérifier si l'utilisateur existe*/ 

        /*-----------------PERMET A L'UTILISATEUR DE SE CONNECTER-------------------*/
        if ($check_presence_user->rowCount() > 0){
            $infoUser = $check_presence_user->fetch();
            /*Va permettre à l'utilidateur de rester connécté et de récupérer ses infos*/
            $_SESSION['userId'] = $infoUser['userId'];
            $_SESSION['userName'] = $infoUser['username'];
            $succesMessageaside = "Welcome ".$_SESSION['userName'];

          } else{
            $errorMessageaside ="Incorrect username or password";
        }
    } else{
        $errorMessageaside ="Please complete all fields";
    }
}
?>

    <aside class="col-md-3 col-12">
                    <!-----------SEARCH-------------->
                <!-- <form action="" method="POST">
                  <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                  <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form> -->
                  <!-----------Message-------------->
                <?php if (isset($errorMessageaside)) { ?> <p style="color: red;"><?= $errorMessageaside ?></p> <?php } ?>
                <?php if (isset($succesMessageaside)) { ?> <p style="color: green;"><?= $succesMessageaside ?></p> <?php } ?>
                
                <!--- Are you connected? No = login or register. Yes = see your profile page --->
                <?php if (empty($_SESSION['userId'])){
                ?> 
                    <!-----------USERNAME-------------->
                    <h3 class="titlelogin" id="login">Login</h3>
                <form action="index.php" method="POST" name="aside">
                  </br>
                  <div class="w-100 col-auto">
                    <label>Username</label>
                    <div class="input-group mb-2">
                      <input type="text" name="userName" class="form-control" id="inlineFormInputGroup" placeholder="Username" maxlength="16">
                    </div>
                    <!-----------PASSWORD-------------->
                    <label>Password</label>
                    <input type="password" name="pass" class="form-control" id="exampleInputPassword1" placeholder="Password">
              
                    <div class="form-group form-check">
                      <input type="checkbox" class="form-check-input" id="exampleCheck1">
                      <label class="form-check-label" for="exampleCheck1">Remember me</label>
                    </div>
                    <button type="submit" name="validateone" class="btn btn-success"><strong>Login</strong></button>
                  </div>
                </form>
                </br>
            <!-----------NEW MEMBER-------------->
            
                <h3 class="Become">Become a member</h3>
                <button type="submit" name="becomeaMembre" class="w-100 btn btn-success">
                <?php echo "<a href='register.php'> <strong>Sign up</strong> ! </a>" ?>
                </button>
                </br>
            <?php 
            }else{
            ?>
                </br>
                <h3 class="Become">My profile</h3>
                <button type="submit" name="myProfil" class="w-100 btn btn-success">
                <?php echo "<a href='profile.php'> <strong>Complete your profile !</strong> </a>" ?>
                </button><br /><br />
                <button type="submit" name="myProfil" class="w-100 btn btn-success">
                <?php echo "<a href='destroy_session.php'> <strong>Log out</strong> </a>" ?>
                </button>
            <?php 
            }
            ?>
            

      <!-----------LAST POSTS -------------->
          <h3 class="titlelastPost">Last Post</h3> 
          <?php
          $lastPost = $bdd->prepare('SELECT postContent, postTopicId FROM posts ORDER BY postId DESC LIMIT 4');
          $lastPost->execute();

          while ($post = $lastPost->fetch(PDO::FETCH_ASSOC)){
            $lastTopic = $bdd->prepare('SELECT * FROM topics WHERE topicId = ?');
            $lastTopic->execute([$post['postTopicId']]);
            $topicTitle=$lastTopic->fetch(PDO::FETCH_ASSOC);
          ?>
              <div class="card bg-light mb-3 lastpost">
                <div class="card-header headergreen">
                <strong>
                <!----- TEST ----->
                <a class="poststitle" href="posts.php?id=<?= $topicTitle["topicId"]; ?>">
					      <?= $topicTitle["topicTitle"]; ?>
			        	</a>
                </strong>
                </div>
                <div class="card-body">
                  <div class="card-text">
                      <?= Michelf\MarkdownExtra::defaultTransform($post['postContent']); ?>
                  </div>
                </div>
              </div>
          <?php
          }
        ?>

        <!-----------LAST ACTIVE USERS -------------->
<h3 class="newmember">New members</h3> 
  <div class="card bg-light col-12 mb-3 mt-3 p-2"> 
    <?php
    $userId = $bdd->prepare('SELECT * FROM users ORDER BY userId DESC LIMIT 3');
    $userId->execute();
    while ($userpost = $userId->fetch(PDO::FETCH_ASSOC)){
      $email = $userpost["userEmail"]; 
      $default = "https://cdn1.iconfinder.com/data/icons/sport-avatar-7/64/05-sports-badminton-sport-avatar-player-512.png";
      $grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=" . urlencode( $default );
    ?>
    <div class="container">
      <div class="row p-2">
        <div class="col-3 justify-content-center">
          <!-- img with the URL created -->
          <img class="newmemberPic" src="<?php echo $grav_url; ?>" alt="picture" />
        </div>
        <div class="col-9 d-flex align-items-center">
          <a  class="profile h4 poststitle" href="profile.php?id=<?= $userpost["userId"]; ?>"><?= $userpost['username']; ?></a>  
        </div>
      </div>
    </div>
      <?php
      }
    ?>
  </div>  

