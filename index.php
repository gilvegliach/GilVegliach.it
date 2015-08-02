<?php
spl_autoload_register(function($class){
    require './libs/markdown/'.preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
});

use \Michelf\Markdown;
$text = file_get_contents("./blog/Espresso - Click on last item in AdapterView.md");
$post = Markdown::defaultTransform($text);

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Mono' rel='stylesheet' type='text/css'>
    
    <link rel='stylesheet' href='styles/main.css' type='text/css'>
    
    <link rel='stylesheet' href='/libs/highlight/styles/github.css' type='text/css'>
    <script src="/libs/highlight/highlight.pack.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
    
    <title>Gil's blog</title>
    
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
        <?php echo $post; ?>
    </div>
</body>
</html>
