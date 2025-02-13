<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>Mi cabecera</header>
    <main>
        <?php 
            if(isset($contenido)) {
                echo $contenido; 
            }
        ?>
    </main>

    <footer>Mi aplicacion</footer>
</body>
</html>