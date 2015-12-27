<?php
    spl_autoload_register(function($class){
        require './libs/markdown/'.preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
    });

    use \Michelf\Markdown;
	include("libs/rrssb/rrssb-buttons.php");

	
	
    function renderPost($id) {
        $query = "SELECT description, content FROM articles WHERE id='$id'";
        $result = mysql_query($query);
        $row_array = mysql_fetch_array($result);
        $post_description = $row_array['description'];
        $post_content = $row_array['content'];
        $post = $post_description . "\n\n" . $post_content;
		$share_description = substr($post_description, 0, strpos($post_description, PHP_EOL));
		$share_link = "http://" . $_SERVER['HTTP_HOST'] . "/?id=$id";
        return "<!-- desc: $share_description link: $share_link -->" .
			renderButton($share_description, $share_link) . Markdown::defaultTransform($post);
    }
    
    function renderAllPosts() {
        $all_posts = "";
        $query = "SELECT id, description FROM articles";
        $result = mysql_query($query);
        while ($row_array = mysql_fetch_array($result)) {
              $id = $row_array['id'];
              $post = Markdown::defaultTransform($row_array['description']);
              $post = $post . "<a href=\"/?id=" . $id . "\"> Read more </a>";
              $all_posts = $post . $all_posts;
        }
        return $all_posts;  
    }
    
    include("secret/configuration.php");
    mysql_connect($db_host, $db_user, $db_pass);
    mysql_select_db($db_name);

    if (isset($_GET['id'])) {
        // The cast prevent SQL injections
        $id = (int)$_GET['id'];
        $content = renderPost($id);
        if (empty(trim($content))) {
            $content = renderAllPosts();
        }
    } else {
        $content = renderAllPosts();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gil's blog</title>
 
    <link href='http://fonts.googleapis.com/css?family=Roboto+Mono' rel='stylesheet' type='text/css'>
    <link href='/libs/highlight/styles/github.css' rel='stylesheet' type='text/css'>

    <script src="/libs/highlight/highlight.pack.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>

    <?php include("common/head.php") ?>
	
	<link rel="stylesheet" href="libs/rrssb/rrssb.css" />
</head>
<body>
    <?php include("common/top.php") ?>   
	
    
    <div id="content">
        <?php echo $content; ?>
    </div>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="libs/rrssb/rrssb.min.js"></script>
</body>
</html>
