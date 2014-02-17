<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        {{ get_title() }}
        <link href="data:image/x-icon;base64,AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAgAAAAAAAAAAAAAAAEAAAAAAAAABHsO0A9/PtAAqf9QDrj4EAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMzMzMzMzMzMzMiIiIiMzMzMyAiIiAzMzMzICICICIzMzMgIgIiIiMzMyIiICIyIzMzIiIgIjIjMzMiAiAiMiMzMSICIiIyIzMxIiIiIjIjMxEhIgISMiMzESEiAhIiMzMRERIhEhMzMzEREREREzMzMxMRMxEzMzMzMzMzMzMzMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA" rel="icon" type="image/x-icon" />
        {{ stylesheet_link('css/bootstrap/bootstrap.min.css') }}
        {{ stylesheet_link('css/bootstrap/docs.css') }}
        {{ stylesheet_link('css/bootstrap/font-awesome.css') }}
        {{ stylesheet_link('css/bootstrap-social.css') }}
        {{ stylesheet_link('css/style.css') }}
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="We recommend the hottest bars, clubs, and restaurants based on the buzz surrounding social media">
        <meta name="author" content="Thomas Lackemann">
    </head>
    <body>
        {{ content() }}
        {{ javascript_include('js/jquery.min.js') }}
        {{ javascript_include('js/bootstrap/bootstrap.min.js') }}
        {{ javascript_include('js/docs.js') }}
        {{ javascript_include('js/scripts.js') }}
        <script>
            // Set the base path of our application (for dev purposes really)
            window.nextspot.setBasePath('{{ url() }}');

            // Google Analytics
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-43143962-3', 'nextspot.us');
            ga('send', 'pageview');

        </script>
    </body>

</html>