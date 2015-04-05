<!DOCTYPE html>
<html>
    <head>
        <title>Error 500</title>
    </head>
    <body>
        error 500
        <?php if (DEBUG): ?>
            <pre><?php print_r($e); ?></pre>
        <?php endif; ?>
    </body>
</html>
