<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test - api</title>
</head>

<body>
<div id="app">
    <div id="main">
        <h1>Statut de l'API</h1>
        <p>Code HTTP : <?= $status['code'] ?></p>
        <pre><?= print_r($status['body'], true) ?></pre>
    </div>
</div>    
</body>

</html>