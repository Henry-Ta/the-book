<?php

    session_start();
        
    include("../classes/connect.php");
    include("../classes/login.php");
    include("../classes/user.php");
    include("../classes/post.php");

    include("get_images.php");

    $login = new Login();
    $user_data = $login->check_login($_SESSION['thebook_userid']);

    // create post
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $post = new Post();
        $result = $post->create_post($_SESSION['thebook_userid'],$_POST);
        if($result){
            header("Location: timeline.php");      // to not resend data to database when reload
            die;
        }
    }

    // get post
    function get_posts(){
        $post = new Post();
        $posts = $post->get_posts($_SESSION['thebook_userid']);

        if($posts){
            foreach($posts as $p){
                $user = new User();
                $data_user = $user->get_data($p['userid']);

                $avatar_user = get_profile_image($data_user['profile_image'],$data_user['gender']);

                echo '<div id="postBackground">
                        <div id="postArea">
                            <div id="userBar">
                                <img id="userImg" src="../images/' . $avatar_user . '">
                                <div id="userName">' . $data_user["first_name"] . " " . $data_user["last_name"] . '</div>
                                <div id="date">' . $p["date"] .'</div>
                            </div>
                            <div id="postContent">
                            ' . $p["post"] . '
                            <br><br>
                            <a href="">Like</a> . <a href="">Comment</a>
                            </div>
                        </div>
                    </div> ';
            }
        }
    }

?>

<!----------------------------------------HTML------------------------------------------->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timeline Page | Thebook</title>
    <link rel="stylesheet" href="../styles/style3.css">
</head>
<body>
    <header>
        <?php include("topbar.php")?>
    </header>
    <main>

        <div id="bodyTimeline">
            <div id="leftContent">
                <a href="profile.php"><img id="userImg" src="<?php print_r($_SESSION['profile_image']) ?>"></a>
                <br>
                <div id="userName"><?php echo $user_data['first_name'] . " ". $user_data['last_name'];?></div>
            </div>
            <div id="centerContent">
                <form method="post">
                    <div id="postForm">
                        <textarea name="post" placeholder=" What's on your mind?"></textarea>
                        <input id="postButton" type="submit" value="Post">                   
                    </div>
                </form>
                <?php
                    get_posts();  
                ?>
            </div>
            <div id="rightContent">
                Request
            </div>
        </div<>
    </main>
    <footer></footer>
</body>
</html>