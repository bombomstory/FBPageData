
<!-- Program by Mr.Paitoon Thipsanthia, Thailand. bombomstory@gmail.com -->

<?php

function loadURL($url)
{
    $json_object = fetchUrl($url);
    $feedarray = json_decode($json_object); //decode json object

    return $feedarray;
}

function fetchUrl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    // You may need to add the line below
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $feedData = curl_exec($ch);
    curl_close($ch);

    return $feedData;
}

function loadSearchResults($url, $access_token)
{
    $json_object = fetchUrl($url);
    //decode json object
    $feedarray = json_decode($json_object);
    if (count($feedarray->data)!=0) {
        foreach ($feedarray->data as $feed_data) {
            $post_id = $feed_data->id;
            // echo $post_id."<br>";
            if (isset($feed_data->message)) {
                $post = $feed_data->message;
            }
            if (strpos($post, "'")) {
                //remove single quote
                $post = str_replace("'", " ", $post);
            }
            echo $post."<br>";
            if (isset($feed_data->picture)) {
                $picture = $feed_data->picture;
                echo "<img src='".$picture."' border=1><br>";
            }
            if (isset($feed_data->shares)) {
                $share = $feed_data->shares->count;
            } else {
                $share = 0;
            }
            echo "จำนวนแชร์ ".$share."<br>";
            if (isset($feed_data->likes)) {
                $urlLike="https://graph.facebook.com/".$post_id."?fields=likes.summary(true)&access_token=".$access_token;
                $feeddata = loadURL($urlLike);
                $numLike = $feeddata->likes->summary->total_count;
            } else {
                $numLike = 0;
            }
            echo "จำนวนผู้กดชอบใจ ".$numLike."<br>";
            if (isset($feed_data->comments)) {
                $urlComment="https://graph.facebook.com/".$post_id."?fields=comments.summary(true){message,from{name}}&access_token=";
                $urlComment.=$access_token;
                $feeddata = loadURL($urlComment);
                $numComment = $feeddata->comments->summary->total_count;
                echo "จำนวนแสดงความคิดเห็น ".$numComment."<br>";
                //decode json object
                if (count($numComment)!=0) {
                    $i = 0;
                    foreach ($feeddata->comments->data as $feed_comment_data) {
                        $i++;
                        echo "ความคิดเห็นที่ ".$i." : ".$feed_comment_data->message;
                        echo " โดย ".$feed_comment_data->from->name."<br>";
                    }
                }
                // End of if (count($feedarray->data)!=0)
            } else {
                $numComment = 0;
            }
            echo "<hr border=0 noshade=true>";
        }
    }
    if (isset($feedarray->paging->next)) {
        $nextURL = $feedarray->paging->next;
    } else {
        $nextURL = null;
    }

    return $nextURL;
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
</head>
<body>

<?php

$access_token = "EAACEdEose0cBAJhByGd8vYLENQJ3L6YnO4Ggx6hOQeaxkcQNeztZANDxa6lteOr2DQCM5y1x1Ovknl7rorZCL05zvWgJrrOipwWOKXtU3gxjcuxMqZAjkzyixi88i6xTMboZBgktAi4vyy5FPh2YCZC6ZCf45dx4d20FZANNohZADbKpDzeuuHDNudNZBJq4o7F0ZD";
// เพจโรงเรียนทุ่งกุลาประชานุสรณ์
$page_id ="646444845502692";

if (!isset($_POST['submitbutt'])) {
    $url = "https://graph.facebook.com/".$page_id."/posts?access_token=".$access_token;
    $nextURL = loadSearchResults($url, $access_token);
} else {
    $nextURL = $_POST['url'];
    $nextURL = loadSearchResults($nextURL, $access_token);
}

?>

<form action="index.php" method="post">
    <input type="text" value="<?php echo $nextURL; ?>" name="url">
    <input type="submit" name="submitbutt" value="Fetch Next Page">
</form>

</body>
</html>