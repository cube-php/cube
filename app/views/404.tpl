<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Error 404 - Cube</title>
    {css('styles')}
</head>
<body>
    <h1 class="title">
        php/cube
    </h1>
    <div class="cube-welcome">
        Error 404
    </div>
    <div class="cube-version">
        Page <b>{$_current_path}</b> not assigned on the <strong>{strtoupper($_request_method)}</strong> request sent.
    </div>
    <div class="cube-version">
        v0.1
    </div>
</body>
</html>