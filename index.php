<?php

include "./twitch-auth/lib/auth.php";

$auth = new TwitchAuth("sshpzug4jo1iflgqnw4vjaxykgv21h", "4prsqey2sz0hkt8rkir7a9lpyonbqa", "http://localhost:8080");

$auth_url = $auth->getTwitchAuthorizationURL();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Twitch Auth Demo</title>
</head>

<body>
  <h1>Example</h1>
  <p>First, include twitch-auth</p>
  <pre>
    <code>
      <?=htmlspecialchars('include "./lib/auth.php";')?>
    </code>
  </pre>

  <p>Create an instance of TwitchAuth</p>
  <pre>
    <code>
      <?=htmlspecialchars('$CLIENT_ID = "sshpzug4jo1iflgqnw4vjaxykgv21h";
      $CLIENT_SECRET = "4prsqey2sz0hkt8rkir7a9lpyonbqa";
      $BASE_URL = "http://localhost:8080";

      $auth = new TwitchAuth($CLIENT_ID, $CLIENT_SECRET, $BASE_URL);')?>
    </code>
  </pre>

  <p>Get Twitch Auth URL and redirect/create link to it (if empty arg, redirect url will be
    $BASE_URL/twitch-auth/callback.php)</p>
  <pre>
    <code>
      <?=htmlspecialchars('$redirect_url = "http://localhost:8080/callback.php";
      $auth->getTwitchAuthorizationURL($redirect_url)')?>
    </code>
  </pre>
  <p>Example of call without args for current instance: <?=var_dump($auth_url)?></p>
</body>

</html>