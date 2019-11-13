<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Home</title>
  <meta name="robots" content="noindex" />
</head>
<body>
  <nav>
    <?php
    include_once './access_tokens.php';
    for ($i = 0; $i < count($access_tokens); $i++) {
      echo '<a href="/spammer.php?access_index='.$i.'">User'.$i.'</a><hr>';
    }
    ?>
  </nav>

  <hr>
  <h3>Author:</h3>
  <div>
    <script src="https://apis.google.com/js/platform.js"></script>

    <div class="g-ytsubscribe" data-channelid="UCXnbTnQDe3v6RR6LXAv6nBg" data-layout="full" data-count="default"></div>
  </div>
</body>
</html>