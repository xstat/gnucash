<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gnucash Reports - @yield('title')</title>
        <link rel="stylesheet" type="text/css" href="css/app.css">
    </head>
    <body>

        <div class="container">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="#">Gnucash Reports</a>
                    </div>
                </div>
            </nav>
            @yield('content')
        </div>
        <script type="text/javascript" src="js/app.js"></script>
    </body>
</html>
