<?php
require_once "common.php";

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

    <?php if (isset($msg)) { print "<div class=\"alert\">$msg</div>"; } ?>
    <?php if (isset($error)) { print "<div class=\"error\">$error</div>"; } ?>
    <?php if (isset($success)) { print "<div class=\"success\">$success</div>"; } ?>

    <p>Now you can set your RTC password for <b><?php print $rtc_id; ?></b>.</p>
    <div id="create-form">
      <form method="post" action="set_password.php">
        <input type="hidden" name="rtc_id" value="<?php print $rtc_id; ?>"/>
        <input type="hidden" name="rtc_token" value="<?php print $rtc_token; ?>"/>
        Set&nbsp;RTC&nbsp;Password:
        <input type="password" name="rtc_password" value="" /><br/>
        Repeat&nbsp;RTC&nbsp;Password:
        <input type="password" name="rtc_password2" value="" />

        <input type="submit" value="Set RTC Password" />
      </form>
    </div>
  </body>
</html>
