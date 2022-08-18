<?php

include "./lib/auth.php";

$code = TwitchAuth::parseCode();

$CLIENT_ID = "sshpzug4jo1iflgqnw4vjaxykgv21h";
$CLIENT_SECRET = "4prsqey2sz0hkt8rkir7a9lpyonbqa";
$BASE_URL = "http://localhost:8080";

$auth = new TwitchAuth($CLIENT_ID, $CLIENT_SECRET, $BASE_URL);


$auth_tokens = $auth->getOAuthTokens($code);
$does_follow = $auth->doesFollow("nvidia");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Callback example</title>
</head>

<body>
  <h1>Example</h1>
  <p>First, include twitch-auth</p>
  <pre>
    <code>
      <?=htmlspecialchars('include "./lib/auth.php";')?>
    </code>
  </pre>
  <p>Then, parse code from callback URL query params (Twitch will redirect with apropriate params)</p>
  <pre>
    <code>
      <?=htmlspecialchars('$code = TwitchAuth::parseCode();')?>
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

  <p>Get OAuth access_token and refresh_token</p>
  <pre>
    <code>
      <?=htmlspecialchars('$auth_tokens = $auth->getOAuthTokens($code);')?>
    </code>
  </pre>
  <p>Contents of $auth_token here are: <?=var_dump($auth_tokens)?></p>
  <p>getOAuthTokens() sets access_token for the same instance of TwitchAuth</p>
  <p>Alternatively, you can set access token on another instance with</p>
  <pre>
    <code>
      <?=htmlspecialchars('$auth->setAccessToken("access_token_value");')?>
    </code>
  </pre>

  <p>After that, you can check if the user which authenticated with Twitch follows given user, e.g:</p>
  <pre>
    <code>
      <?=htmlspecialchars('$does_follow = $auth->doesFollow("nvidia");')?>
    </code>
  </pre>
  <p>Here, it's: <?=var_dump($does_follow)?></p>
</body>

</html>