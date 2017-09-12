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

        // Don't render anything is post is not available
        if (empty(trim($post))) {
            return "";
        }
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
              $post = "<div class=\"post_description\">" .
                $post . "<a class=\"post_link\" href=\"/?id=" . 
                $id . "\"> Read more </a></div>";
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

    $redirects = array(
      1  => 'https://clevercoder.net/2017/02/19/espresso-click-on-last-item-in-adapterview/',
      2  => 'https://clevercoder.net/2016/10/14/basics-bit-manipulation/',
      3  => 'https://clevercoder.net/2017/08/01/library-draws-transparent-text-textview/',
      4  => 'https://clevercoder.net/2017/03/07/retained-fragment-trick/',
      5  => 'https://clevercoder.net/2017/01/03/adb-over-wifi/',
      6  => 'https://clevercoder.net/2017/04/11/git-committed-master-instead-forking-branch/',
      7  => 'https://clevercoder.net/2016/12/12/getting-annotation-value-enum-constant/',
      8  => 'https://clevercoder.net/2017/03/29/git-getting-back-amended-commit/',
      9  => 'https://clevercoder.net/2017/06/15/retrofit-2-code-walkthrough/',
      10 => 'https://clevercoder.net/2017/05/23/hidden-activities-not-destroyed-memory-pressure/',
      11 => 'https://clevercoder.net/2016/11/18/three-tools-stay-zone/',
      12 => 'https://clevercoder.net/2017/07/06/loading-dex-code-network/',
      13 => 'https://clevercoder.net/2016/04/20/google-code-jam-2016-qualification-round-passed/'
    );

    mysql_connect($db_host, $db_user, $db_pass);
    mysql_select_db($db_name);

    // http://stackoverflow.com/questions/12194205/how-to-check-whether-a-variable-in-get-array-is-an-integer
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!is_null($id)) {
        if (array_key_exists($id, $redirects)) {
            header('Location: ' . $redirects[$id], true, 301); // Moved Permanently
            die();
        }
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
