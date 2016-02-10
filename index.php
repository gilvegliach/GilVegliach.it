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
        $share_description = extractTitle($post_description);
        $share_link = resolveUrl($id);
        return renderButton($share_description, $share_link)
            . Markdown::defaultTransform($post)
            . renderComments($id);
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
    
    function renderPostTitle($id) {
        $query = "SELECT description FROM articles WHERE id='$id'";
        $result = mysql_query($query);
        $row_array = mysql_fetch_array($result);
        return extractTitle($row_array['description']);
    }
    
    function renderComments($id) {
        $url = resolveUrl($id);
        $comments = <<<END
            <div id="disqus_thread"></div>
            <script>
            var disqus_config = function() {
                this.page.url = "$url";
                this.page.identifier = "$id";
            };
            (function() {  
                var d = document, s = d.createElement('script');
                s.src = '//gilvegliachit.disqus.com/embed.js';  
                s.setAttribute('data-timestamp', +new Date());
                (d.head || d.body).appendChild(s);
            })();
            </script>
                <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">
                comments powered by Disqus.</a></noscript>
END;
        return $comments;
    }

    function resolveUrl($id) {
        return "http://" . $_SERVER['HTTP_HOST'] . "/?id=$id";
    }

    function extractTitle($description) {
       return substr($description, 0, strpos($description, PHP_EOL));
    }
   
    function renderAllPostTitle(){
        return "Gil's blog";
    }
    
     
    include("secret/configuration.php");
    mysql_connect($db_host, $db_user, $db_pass);
    mysql_select_db($db_name);

    if (isset($_GET['id'])) {
        // The cast prevent SQL injections
        $id = (int)$_GET['id'];
        $content = renderPost($id);
        if (!empty(trim($content))) {
            $title = renderPostTitle($id);
        } else {
            $title = renderAllPostTitle();
            $content = renderAllPosts();
        }
    } else {
        $title = renderAllPostTitle();
        $content = renderAllPosts();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
 
    <link href="http://fonts.googleapis.com/css?family=Roboto+Mono" rel="stylesheet" type="text/css">
    <link href="/libs/highlight/styles/github.css" rel="stylesheet" type="text/css">
    <link href="/libs/rrssb/rrssb.css" rel="stylesheet" />
	
    <script src="/libs/highlight/highlight.pack.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>

    <?php include("common/head.php") ?>
</head>
<body>
    <?php include("common/top.php") ?>
    <div id="content"><?= $content; ?></div>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="/libs/rrssb/rrssb.min.js"></script>
</body>
</html>
