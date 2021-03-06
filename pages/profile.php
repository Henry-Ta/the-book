<?php
    session_start();
    
    include("../classes/connect.php");
    include("../classes/login.php");
    include("../classes/user.php");
    include("../classes/post.php");
    
    include("get_images.php");

    $profile_image = '';        #default profile image
    $background_image = '';     #default cover image

    $login = new Login();
    $user_data = $login->check_login($_SESSION['thebook_userid']);

    $user = new User();
    $post = new Post();

    $gender_user = $user_data['gender'];

    // create post
    if($_SERVER['REQUEST_METHOD']=='POST'){
        global $post;
        $result = $post->create_post($_SESSION['thebook_userid'],$_POST,$_FILES);

        if($result){
            header("Location: profile.php");      // to not resend data to database when reload
            die;
        }

        if(isset($_POST['move_to_friend_page'])){
            // Click on friend image in Friend List and move to their page
            // Using image button to submit and hidden form to take userid
            $_SESSION['found_user'] = $_POST['move_to_friend_page'];
            header("Location: other_user_profile.php");
            die;
        }
    }

    function get_posts(){
        global $post;
        global $user;
        $posts = $post->get_posts($_SESSION['thebook_userid']);
        
        if($posts){
            foreach($posts as $p){
                $image = '';
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

    function get_posts_from_guest(){
        global $post;
        global $user;
        $posts = $post->get_posts_on_other_user($_SESSION['thebook_userid']);
        
        if($posts){
            foreach($posts as $p){
                $image = '';
                $data_user = $user->get_data($p['guestid']);
                $avatar_user = get_profile_image($data_user['profile_image'],$data_user['gender']);

                if(file_exists($p["image"])){
                    $image = '<img src=' . $p["image"] . ' />';
                }
                echo '<div id="postBackground">
                        <div id="postArea">
                            <form method="post">
                                <div id="userBar">
                                    <input type="image" id="userImg" alt="Submit" src="../images/' . $avatar_user . '">
                                    <input type="hidden" name="move_to_friend_page" value="'.$data_user['userid'].'">
                                    <div id="userName">' . $data_user["first_name"] . " " . $data_user["last_name"] . '</div>
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

    function get_friends(){
        global $user;

        $friends = $user->get_friends($_SESSION['thebook_userid']);

        if($friends){
            foreach($friends as $i){
                $f = $user->get_data($i['from_userid']);
                echo '  <form method="post" >
                            <div id="friend">
                                <input type="image" id="friendImg" alt="Submit" src="' . get_profile_image($f['profile_image'],$f['gender']) .'">
                                <input type="hidden" name="move_to_friend_page" value="'.$f['userid'].'">
                                <div id="friendName">' . $f['first_name'] . " " . $f['last_name'] . '</div>
                            </div>
                        </form>';
            }
        }
    }

    $profile_image = get_profile_image($user_data['profile_image'],$gender_user);
    $background_image = get_background_image($user_data['cover_image']);

    $_SESSION['profile_image'] = $profile_image;        // pass value to topbar in other pages

?>

<!----------------------------------------HTML------------------------------------------->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page | Thebook</title>
    <link rel="stylesheet" href="../styles/style2.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="drop_down_topBar.js"></script>
</head>
<body>
    <header>
        <?php include("topbar.php")?>
    </header>
    <main>
        <div id="backgroundCover">
            <div id="coverArea">
                <a href="change_coverImg.php">
                    <img id="coverPhoto" src="<?php echo $background_image ?>">
                </a>
                <a href="change_profileImg.php">
                    <img id="profilePhoto" src="<?php echo $profile_image ?>">
                </a>
                <br>
                <div id="profileName"><?php echo $user_data['first_name'] . " " . $user_data['last_name']?></div>
                <br>
                <div id="menuButtons">
                    <div id="timeline"><a href="timeline.php">Timeline</a></div> 
                    <div id="about"><a href="about.php">About</a></div> 
                    <div id="friends"><a href="friends.php">Friends</a></div> 
                    <div id="photos"><a href="documentation.php">Documentation<a></div> 
                    <div id="settings"><a href="sources.php">Sources</a></div>
                </div>
            </div>
        </div>

        <div id="bodyProfile">
            <div id="leftContent">
                <div id="friendsArea">
                    <div id="friendsBar">
                        <div id="title">Friends</div>
                        <div id="seeAll">See All</div>
                    </div>
                    <div id="friendsList">
                        <?php
                            get_friends();  
                        ?>
                    </div>
                </div>
                <div id="reference">
                    <ul id="referenceList">
                        <li id="privacy">Privacy</li>
                        <li id="terms">Terms</li>
                        <li id="advertising">Advertising</li>
                        <li id="more">More</li>
                    </ul>
                    Henry Ta @ 2020 ( ^ o ^)
                </div>
            </div>
            <div id="rightContent">
                <form method="post" enctype="multipart/form-data">
                    <div id="postForm">
                        <textarea name="post" placeholder=" What's on your mind?"></textarea>
                        <input id="file" type="file" name="file">
                        <input id="postButton" type="submit" value="Post">                   
                    </div>
                </form>

                <?php
                    get_posts();  
                    get_posts_from_guest();
                ?>

            </div>
        </div<>
    </main>
    <footer></footer>
</body>
</html>