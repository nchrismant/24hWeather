<?php
require_once "vendor/autoload.php";

require_once "./class/Mail/PHPMailer/src/Exception.php";
require_once "./class/Mail/PHPMailer/src/PHPMailer.php";
require_once "./class/Mail/PHPMailer/src/SMTP.php";

use Météo\Mail\Mail;
use PHPMailer\PHPMailer\PHPMailer;

?>
    <footer>
      <div class="main-content">
        <div class="left box">
          <h2>À propos</h2>
          <div class="content">
            <p>24hWeather est un site météorologique basé sur plusieurs API dont notamment OpenWeather. Ce site permet de retrouver pour chaque coin du monde, la météo actuelle en temps réel ainsi que les prévisions météorologiques pour les jours à venir.</p>
          </div>
        </div>
        <div class="center box">
          <h2>Adresse</h2>
          <div class="content">
            <div class="place">
              <span class="fas fa-map-marker-alt"></span>
              <span class="text-footer">CY CERGY PARIS UNIVERSITÉ</span>
              <span class="text-footer2">33 BOULEVARD DU PORT</span>
              <span class="text-footer2">95011 CERGY-PONTOISE</span>
              <span class="text-footer2">FRANCE</span>            
            </div>
          </div>
        </div>
        <div class="right box">
          <h2>Nous Contacter</h2>
          <div class="content">
            <form action="#" method="POST">
              <div class="email">
                <div class="text-footer">Votre Nom *</div>
                <input type="text" name="nameMail" required="required"/>
              </div>
              <div class="email">
                <div class="text-footer">Votre Email *</div>
                <input type="email" name="userMail" required="required"/>
              </div>
              <div class="msg">
                <div class="text-footer">Votre Message *</div>
                <textarea rows="2" cols="25" name="bodyMail" required="required"></textarea>
              </div>
              <div class="btn-footer">
                <button type="submit">Envoyer</button>
              </div>
            </form>
            <?php
            if(isset($_POST['nameMail'], $_POST['userMail'], $_POST['bodyMail'])) {
              $nameMail = htmlspecialchars($_POST['nameMail']);
              $userMail = htmlspecialchars($_POST['userMail']);
              $bodyMail = htmlspecialchars($_POST['bodyMail']);
              $phpmailer = new PHPMailer();
              $mail = new Mail($phpmailer);
              $mail->sendMail($bodyMail, $userMail, $nameMail);
            }
            ?>
          </div>
        </div>
      </div>
      <div class="bottom">
        <div id="center">
          <span class="credit">Réalisé par <a>CHRIQUI Nathan</a> | </span>
          <span class="far fa-copyright"></span><span> 2021 Tous droits réservés.</span>
        </div>
      </div>
    </footer>
    <div id="scrolltotop">
      <a class="arrow-up">
        <span class="left-arm"></span>
        <span class="right-arm"></span>
        <span class="arrow-slide"></span>
      </a>
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <?php
    $page = basename($_SERVER["PHP_SELF"]);
    if($page == "world.php") {
      echo "<script src=\"https://unpkg.com/leaflet@1.7.1/dist/leaflet.js\" integrity=\"sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==\" crossorigin=\"\"></script>\n";
      echo "\t<script src=\"./leaflet/leaflet.control.geocoder.min.js\"></script>\n";
      echo "\t<script src=\"./leaflet/leaflet.legend.min.js\"></script>\n";
      if(isset($_SESSION['auth'])) {
        echo "\t<script>\n";
        echo "\t\tvar id = ".json_encode($_SESSION['auth']).";\n";
        if(!empty($fav)) {
          echo "\t\tvar favs = ".json_encode($fav).";\n";
        }
        echo "\t</script>\n";
      }
      echo "\t<script src=\"./leaflet/lf.min.js\"></script>\n";
    }
    if($page == "meteo.php") {
      echo "<script src=\"https://unpkg.com/leaflet@1.7.1/dist/leaflet.js\" integrity=\"sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==\" crossorigin=\"\"></script>\n";
      echo "\t<script>\n";
      if(!empty($citypos)) {
        echo "\t\tvar citypos = ".json_encode($citypos).";\n";
      }        
      if(isset($_SESSION['auth'])) {
        echo "\t\tvar id = ".json_encode($_SESSION['auth']).";\n";
      }
      echo "\t</script>\n";
      echo "\t<script src=\"mapcity.min.js\"></script>\n";
    }
    ?>
    <script src="script.min.js"></script>
  </body>
</html>