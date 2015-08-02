<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400' rel='stylesheet' type='text/css'>
    <link href='styles/main.css' rel='stylesheet' type='text/css'>
    
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
        <h1>Espresso: Click on last item in AdapterView</h1>

        <p>In Espresso is quite easy to tap on the first element of an <code>AdapterView</code>, such
        as a <code>ListView</code>. This can be easily done calling <code>DataIteraction.atPosition(0)</code>. 
        Clicking on the last item though, is much more complicated. The last position is 
        unknown to Espresso and extracting it stringing together a <code>findViewById()</code> and
        <code>AdapterView.getCount()</code> seems to defeat the purpose of using Espresso 
        altogether.</p>

        <p>Luckily there is a simple solution to this problem: let Espresso see the items
        in reversed order and then click on the first item. This can be accomplished
        with a custom <code>AdapterViewProtocol</code>.</p>

        <p><code>AdapterViewProtocol</code> is a simple interface that defines how Espresso interacts
        with <code>AdapterViews</code>. It supports four operations:</p>

        <ol>
        <li>Get all data items in the adapter</li>
        <li>Given a child view, return the corresponding data item</li>
        <li>Given a data item, ask whether it is displayed by some child view</li>
        <li>Force a data item to be displayed by a child view</li>
        </ol>

        <p>For our use-case we just need to get the complete dataset and reverse it, then
        it can be used like this:</p>

        <pre><code>onData(instanceOf(MyAdapterItem.class))
            .atPosition(0)                                       
            .usingAdapterViewProtocol(new ReverseProtocol())
            .perform(click());
        </code></pre>

        <p>Here comes the code for the protocol. Note that the standard protocol is a
        private class so it can't be extended, so we delegate to it: </p>

        <pre><code>public class ReverseProdocol implements AdapterViewProtocol {
            private final AdapterViewProtocol delegate = standardProtocol();

            @Override
            public Iterable&lt;AdaptedData&gt; getDataInAdapterView(
                    AdapterView&lt;? extends Adapter&gt; adapterView) {
                LinkedList&lt;AdaptedData&gt; result = new LinkedList&lt;&gt;();
                for (AdaptedData data : delegate.getDataInAdapterView(adapterView)) {
                    result.addFirst(data);
                }
                return result;
            }

            @Override
            public Optional&lt;AdaptedData&gt; getDataRenderedByView(
                    AdapterView&lt;? extends Adapter&gt; adapterView, View view) {
                return delegate.getDataRenderedByView(adapterView, view);
            }

            // Similarly delegate to the other two methods
            // ...
        }
        </code></pre>
        
    </div>
</body>
</html>
