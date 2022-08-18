<?php

class FailedAuthorizationException extends Exception {}

class TwitchAuth {
  protected string $CLIENT_ID;
  protected string $CLIENT_SECRET;
  protected string $BASE_URL;
  protected string $accessToken;

  public function __construct(string $CLIENT_ID, string $CLIENT_SECRET, string $BASE_URL) {
    $this->CLIENT_ID = $CLIENT_ID;
    $this->CLIENT_SECRET = $CLIENT_SECRET;
    $this->BASE_URL = $BASE_URL;
  }

  public function getTwitchAuthorizationURL(string $redirect_url = "") {
    $redirect_uri = $redirect_url;
    if(strcmp($redirect_uri, "") == 0) {
      $redirect_uri = $this->BASE_URL."/twitch-auth/callback.php";
    }

    $data = array(
      "client_id" => $this->CLIENT_ID,
      "redirect_uri" => $redirect_uri,
      "response_type" => "code",
      "scope" => "user:read:follows"
    );

    return "https://id.twitch.tv/oauth2/authorize?".http_build_query($data);
  }

  public static function parseCode() {
    if(!isset($_GET['code'])) {
      throw new Exception("Code not found in query params");
    }

    if(isset($_GET['error'])) {
      throw new FailedAuthorizationException("User didn't authorize app");
    }

    return $_GET['code'];
  }

  public function getOAuthTokens(string $code) {
    $data = array(
      "client_id" => $this->CLIENT_ID,
      "client_secret" => $this->CLIENT_SECRET,
      "code" => $code,
      "grant_type" => "authorization_code",
      "redirect_uri" => "http://localhost:8080/twitch-auth/callback.php"
    );

    $ch = curl_init("https://id.twitch.tv/oauth2/token?".http_build_query($data));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    $response_string = curl_exec($ch);
    $response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

    if(strcmp($response_code, "200") == 0) {
      $response = json_decode($response_string, true);

      $access_token = $response["access_token"];
      $refresh_token = $response["refresh_token"];

      $this->accessToken = $access_token;

      return array(
        "access_token" => $access_token,
        "refresh_token" => $refresh_token
      );
    } else {
      throw new Exception("Failed to get OAuth tokens! HTTP code: ".$response_code);
    }
  }

  public function setAccessToken(string $access_token) {
    $this->accessToken = $access_token;
  }

  protected function getAccessToken() {
    if(isset($this->accessToken)) {
      return $this->accessToken;
    }

    throw new Exception("Access token not found!");
  }

  public function getUserId(string $user_login = null) {
    $access_token = $this->getAccessToken();

    $headers = array(
      "Authorization: Bearer $access_token",
      "Client-Id: ".$this->CLIENT_ID
    );

    $data = array();

    if(isset($user_login)) {
      $data["login"] = $user_login;
    }

    $ch = $this->getAuthorizedRequest("https://api.twitch.tv/helix/users?".http_build_query($data));

    $response_string = curl_exec($ch);
    $response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

    if(strcmp($response_code, "200") == 0) {
      $response = json_decode($response_string, true);
      if(count($response['data']) == 0) {
        throw new Exception("No matching user found!");
      }
      return $response['data'][0]['id'];
    }

    throw new Exception("Failed to get user ID! HTTP code: ".$response_code);
  }

  public function doesFollow(string $followed_login) {
    $user_id = $this->getUserId();
    $followed_id = $this->getUserId($followed_login);

    $ch = $this->getAuthorizedRequest("https://api.twitch.tv/helix/users/follows?from_id=$user_id&to_id=$followed_id&first=100");

    $response_string = curl_exec($ch);
    $response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

    if(strcmp($response_code, "200") == 0) {
      $response = json_decode($response_string, true);

      return $response["total"] >= 1;
    }
  }

  private function getAuthorizedRequest(string $url) {
    $access_token = $this->getAccessToken();

    $headers = array(
      "Authorization: Bearer $access_token",
      "Client-Id: ".$this->CLIENT_ID
    );

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    return $ch;
  }
}

?>