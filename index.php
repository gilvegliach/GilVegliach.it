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
 
    <link href='http://fonts.googleapis.com/css?family=Roboto+Mono' rel='stylesheet' type='text/css'>
    <link href='/libs/highlight/styles/github.css' rel='stylesheet' type='text/css'>

    <script src="/libs/highlight/highlight.pack.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>

    <?php include("common/head.php") ?>
</head>
<body>
    <?php include("common/top.php") ?>   
    <div id="content">
        <?php echo $content; ?>
    </div>
</body>
</html>
