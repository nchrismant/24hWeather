<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- méta-données -->
        <title>Page introuvable - 24h Weather</title>
        <meta name="author" content="nathan"/>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous"/>
        <link type="text/css" rel="stylesheet" title="standard" href="styles.min.css"/>
        <link rel="icon" href="./images/favicon.ico"/>
    </head>
    <body>
    <div id="error-page">
         <div class="content-404">
            <h2 class="header" data-text="404">
               404
            </h2>
            <h4 data-text="Opps! Page not found">
               Oups ! Page introuvable !
            </h4>
            <p>
               Désolé, la page que vous recherchez n’a pas été trouvée.
            </p>
            <div class="btns">
               <a href="index.php">Retour page d'accueil</a>
            </div>
         </div>
      </div>
  </body>
</html>