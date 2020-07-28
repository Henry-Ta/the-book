<?php
    if($_SERVER['REQUEST_METHOD']=='POST'){
        if(isset($_POST['search'])){
            $search_user = new User();
            $found_user = $search_user->find_user($_POST['search']);
            if($found_user){
                $_SESSION['found_user'] = $found_user['userid'];
                header("Location: other_user_profile.php");      // to not resend data to database when reload
                die;
            }
        }
    }
?>


<div id="blueBar">
    <div id="headerProfile">
        <div id="logo"><a href="timeline.php">thebook</a></div>
        <div id="search">
            <form method="post">
                <input name="search" type="text" id="searchBox" placeholder="Search Thebook">
                <button type="submit" id="imageButton"></button>
            </form>
        </div>
        <div id="profileImage">
            <a href="profile.php"><img src="<?php print_r($_SESSION['profile_image']) ?>"></a>
            <a href="logout.php" style="padding-left: 5px"><img src="../images/poweroff.png"></a>
            
        </div>
    </div>
</div>

