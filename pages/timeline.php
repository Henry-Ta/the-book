<?php

    session_start();
        
    include("../classes/connect.php");
    include("../classes/login.php");
    include("../classes/user.php");
    include("../classes/post.php");
    include("../classes/friend.php");

    include("get_images.php");

    $login = new Login();
    $user_data = $login->check_login($_SESSION['thebook_userid']);

    $post = new Post();
    $user = new User();
    $friend = new Friend();

    // create post
    if($_SERVER['REQUEST_METHOD']=='POST'){
        global $post;

        $result = $post->create_post($_SESSION['thebook_userid'],$_POST,$_FILES);
        if($result){
            header("Location: timeline.php");      // to not resend data to database when reload
            die;
        }

        //------------------------------------Confirm/Delete friend request--------//
        if(isset($_POST['yes_button'])){
            #echo $_POST['form_id'];     // get id of accepting user
            $result = $friend->accept_request($_POST['form_id'],$_SESSION['thebook_userid']);
            if($result){
                header("Location: timeline.php");      // to not resend data to database when reload
                die;
            }
        }
        if(isset($_POST['no_button'])){
            //echo $_POST['form_id'];     // get id of deleting user
            $result = $friend->delete_request($_POST['form_id'],$_SESSION['thebook_userid']);
            if($result){
                header("Location: timeline.php");      // to not resend data to database when reload
                die;
            }
        }

        // move to other user page
        if(isset($_POST['move_to_friend_page'])){
            // Click on friend image in Friend List and move to their page
            // Using image button to submit and hidden form to take userid
            if($_POST['move_to_friend_page'] != $_SESSION['thebook_userid']){
                $_SESSION['found_user'] = $_POST['move_to_friend_page'];
                header("Location: other_user_profile.php");
                die;
            }else{
                header("Location: profile.php");
                die;
            }  
        }
    }

    // get post
    function get_posts(){
        global $post;
        $posts = $post->get_posts($_SESSION['thebook_userid']);
        
        if($posts){
            foreach($posts as $p){
                $image = '';
                $user = new User();
                $data_user = $user->get_data($p['userid']);

                $avatar_user = get_profile_image($data_user['profile_image'],$data_user['gender']);

                if(file_exists($p["image"])){
                    $image = '<img src=' . $p["image"] . ' />';
                }

                echo '<div id="postBackground">
                        <div id="postArea">
                            <div id="userBar">
                                <img id="userImg" src="../images/' . $avatar_user . '">
                                <div id="userName">' . $data_user["first_name"] . " " . $data_user["last_name"] . '</div>
                                <div id="date">' . $p["date"] .'</div>
                            </div>
                            <div id="postContent">    
                                <div id="post">' . $p["post"] . '</div>
                                <br><br>
                                <div id="image">' . $image .'</div>
                                <br><br>
                                <a href="">Like</a> . <a href="">Comment</a>
                            </div>
                        </div>
                    </div> ';
            }
        }
    }

    // get post from friends
    function get_posts_from_friends(){
        global $post;
        global $user;

        $friends = $user->get_friends($_SESSION['thebook_userid']);
        
        if($friends){
            foreach($friends as $i){
                $f = $user->get_data($i['from_userid']);
                $posts = $post->get_posts($f['userid']);

                if($posts){
                    foreach($posts as $p){
                        $image = '';
                        $avatar_user = get_profile_image($f['profile_image'],$f['gender']);
        
                        if(file_exists($p["image"])){
                            $image = '<img src=' . $p["image"] . ' />';
                        }
        
                        echo '<div id="postBackground">
                                <div id="postArea">
                                    <form method="post">
                                        <div id="userBar">
                                            <input type="image" id="userImg" alt="Submit" src="../images/' . $avatar_user . '">
                                            <input type="hidden" name="move_to_friend_page" value="'.$f['userid'].'">
                                            <div id="userName">' . $f["first_name"] . " " . $f["last_name"] . '</div>
                                            <div id="date">' . $p["date"] .'</div>
                                        </div>
                                    </form>
                                    <div id="postContent">    
                                        <div id="post">' . $p["post"] . '</div>
                                        <br><br>
                                        <div id="image">' . $image .'</div>
                                        <br><br>
                                        <a href="">Like</a> . <a href="">Comment</a>
                                    </div>
                                </div>
                            </div> ';
                    }
                }
            }
        }
    }

    function get_friend_request(){
        global $friend;
        $result = $friend->get_all_requests($_SESSION['thebook_userid']);

        $user = new User();
        
        if($result){
            foreach($result as $r){
                $user_request = $user->get_data($r['from_userid']);
                echo '<div id="friend">
                        <form method="post">
                            <img id="friendImg" src="'. get_profile_image($user_request['profile_image'],$user_request['gender']) . '">
                            <div id="friendName">' . $user_request['first_name'] . " " . $user_request['last_name'] . '</div>
                            <div id="button">
                                <input id="yesButton" type="submit" name="yes_button" value="Confirm" attr="">
                                <input id="noButton" type="submit" name="no_button" value="Delete">
                                <input type="hidden" name="form_id" value='. $r['from_userid'] .'>
                            </div>
                         </form>   
                    </div>';
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="drop_down_topBar.js"></script>
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
                <form method="post" enctype="multipart/form-data">
                    <div id="postForm">
                        <textarea name="post" placeholder=" What's on your mind?"></textarea>
                        <input id="file" type="file" name="file">
                        <input id="postButton" type="submit" value="Post">                   
                    </div>
                </form>
                <?php
                    get_posts(); 
                    get_posts_from_friends();  
                ?>
            </div>
            <div id="rightContent">
                <div id="request">Friend Request</div>
                <?php
                     
                    get_friend_request();
                ?>
            </div>
        </div<>
    </main>
    <footer></footer>
</body>
</html>