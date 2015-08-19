<?php
    spl_autoload_register(function($class){
        require './libs/markdown/'.preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
    });

    use \Michelf\Markdown;

    include("secret/configuration.php");

    mysql_connect($db_host, $db_user, $db_pass);
    mysql_select_db($db_name);

    $query = "SELECT * FROM articles";

    $result = mysql_query($query);
    while ($row_array = mysql_fetch_array($result)) {
          // $id = $row_array['id'];
          $post = Markdown::defaultTransform($row_array['content']);
          $content = $post . $content;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gil's blog</title>
    
    <?php include("common/head.php") ?>
 
    <link href='http://fonts.googleapis.com/css?family=Roboto+Mono' rel='stylesheet' type='text/css'>
    <link href='/libs/highlight/styles/github.css' rel='stylesheet' type='text/css'>

    <script src="/libs/highlight/highlight.pack.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>   
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-2526228-1']);
        _gaq.push(['_trackPageview']);
        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>
    <script type="text/javascript">
        _uacct = "UA-2526228-1";
        urchinTracker();
    </script>
</head>
<body>
    <?php include("common/top.php") ?>   
    <div id="content">
        <?php echo $content; ?>
    </div>
</body>
</html>
