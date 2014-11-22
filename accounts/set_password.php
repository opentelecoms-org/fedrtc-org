<?php
require_once "common.php";
require_once "config.php";

global $pape_policy_uris;
?>
<html>
  <head><title>Fedora RTC account creation</title></head>
  <style type="text/css">
      * {
        font-family: verdana,sans-serif;
      }
      body {
        width: 50em;
        margin: 1em;
      }
      div {
        padding: .5em;
      }
      table {
        margin: none;
        padding: none;
      }
      .alert {
        border: 1px solid #e7dc2b;
        background: #fff888;
      }
      .success {
        border: 1px solid #669966;
        background: #88ff88;
      }
      .error {
        border: 1px solid #ff0000;
        background: #ffaaaa;
      }
      #verify-form {
        border: 1px solid #777777;
        background: #dddddd;
        margin-top: 1em;
        padding-bottom: 0em;
      }
  </style>
  <body>
    <h1>Fedora RTC account creation</h1>

    <?php
        $rtc_id = $_POST['rtc_id'];
        $rtc_token = $_POST['rtc_token'];
        $rtc_password = $_POST['rtc_password'];
        $rtc_password2 = $_POST['rtc_password2'];

        $rtc_token_check = hash_hmac("sha256", $rtc_id, $auth_secret);

        if(strlen($rtc_password) < 4)
        {
            $error = "Password too short, must be at least 4 characters";
        }
        else if($rtc_password != $rtc_password2)
        {
            $error = "Password mismatch.  Must repeat the same password twice.";
        }
        else if($rtc_token_check != $rtc_token)
        {
            $error = "Authentication token mismatch.";
        }
        if(!isset($error))
        {
            $dbconn = pg_connect($pg_creds);
            if(!$dbconn)
            {
                $error = "PostgreSQL connection failed: ".pg_last_error();
            }
            else
            {
                $rtc_id_r = explode('@', strtolower($rtc_id));
                $rtc_id_username = $rtc_id_r[0];
                $rtc_id_domain = $rtc_id_r[1];

                $rtc_password_ha1 = md5($rtc_id_username.":".$rtc_id_domain.":".$rtc_password);
                $rtc_password_ha1b = md5(strtolower($rtc_id).":".$rtc_id_domain.":".$rtc_password);

                $result = pg_query("DELETE FROM users WHERE username = '".$rtc_id_username."' AND domain = '".$rtc_id_domain."'");
                if(!$result)
                {
                    error_log("Deleting old record failed: ".pg_last_error());
                }
                else
                {
                    pg_free_result($result);
                }
                $result = pg_query("INSERT INTO users (username, domain, realm, passwordhash, passwordhashalt) VALUES ('".$rtc_id_username."', '".$rtc_id_domain."', '".$rtc_id_domain."', '".$rtc_password_ha1."', '".$rtc_password_ha1b."')");
                if(!$result)
                {
                    $error = "PostgreSQL query failed: ".pg_last_error();
                }
                else
                {
                    pg_free_result($result);
                    $msg = "Your RTC password has been set.";
                }
                pg_close($dbconn);
            }
        }
    ?>

    <?php if (isset($msg)) { print "<div class=\"alert\">$msg</div>"; } ?>
    <?php if (isset($error)) { print "<div class=\"error\">$error</div>"; } ?>
    <?php if (isset($success)) { print "<div class=\"success\">$success</div>"; } ?>

    <p>Your RTC user ID for SIP, XMPP, WebRTC, TURN and related services is <b><?php print $rtc_id; ?></b></p>
    <p>Use the full username@domain, not just the username part, as the authentication username for all associated services such as SIP and TURN authentication.</p>
    <p>Go back to <a href="http://fedrtc.org">the FedRTC.org WebRTC portal</a> or see the <a href="https://fedoraproject.org/wiki/UnifiedCommunications/CommunityFacilities">Fedora RTC facilities wiki</a>.</p>

  </body>
</html>
