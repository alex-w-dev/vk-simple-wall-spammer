<?php
// to get access token follow link
// https://oauth.vk.com/authorize?client_id=2890984&scope=notify%2Cphotos%2Cfriends%2Caudio%2Cvideo%2Cnotes%2Cpages%2Cdocs%2Cstatus%2Cquestions%2Coffers%2Cwall%2Cgroups%2Cmessages%2Cnotifications%2Cstats%2Cads%2Coffline&redirect_uri=http://api.vk.com/blank.html&display=page&response_type=token
// after that change this file and change $access_token (must be this:
// $access_token = '5945667da41fa779dafa7480472814dee4f1ff390c677cd22e78ablahblahblahblahblahblahblahblah';
//)
$access_token = '';

if (!$access_token) {
  echo 'Follow link<br>';
  echo '<a href="https://oauth.vk.com/authorize?client_id=2890984&scope=notify%2Cphotos%2Cfriends%2Caudio%2Cvideo%2Cnotes%2Cpages%2Cdocs%2Cstatus%2Cquestions%2Coffers%2Cwall%2Cgroups%2Cmessages%2Cnotifications%2Cstats%2Cads%2Coffline&redirect_uri=http://api.vk.com/blank.html&display=page&response_type=token">get new access token</a>';
  echo '<br>';
  echo 'And change your server file '. __FILE__ . ' according instructions';
  exit();
}

function HTTPPost($url, array $params)
{
  $query = http_build_query($params);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
  $response = curl_exec($ch);
  curl_close($ch);
  return $response;
}

?>
<?php
if (isset($_GET['my_videos'])) {
  $getInfoGroupResponse = file_get_contents(
    'https://api.vk.com/method/utils.resolveScreenName'.
    '?screen_name=chertkov.alexandr'.
//  '&count=1'.
    '&access_token='.$access_token.
    '&v=5.102'
  );
  $parsedInfo = json_decode($getInfoGroupResponse, true);

  header('Content-Type: application/json');
  echo file_get_contents('https://api.vk.com/method/video.get'.
    '?owner_id='.(($parsedInfo['response']['type']==='user')?'':'-').$parsedInfo['response']['object_id'].
    '&count=10'.
    '&access_token='.$access_token.
    '&v=5.102');
  exit();
}
if (isset($_POST['start_spam'])) {
  $groups = explode(" ", $_POST['groups']);

  for ($i = 0; $i < count($groups); $i++) {
    $group = $groups[$i];
    $getInfoGroupResponse = file_get_contents(
      'https://api.vk.com/method/utils.resolveScreenName'.
      '?screen_name='.$group.
      '&access_token='.$access_token.
      '&v=5.102'
    );
    $parsedInfo = json_decode($getInfoGroupResponse, true);
    $parsedInfo['response']['object_id'];

    $vk_post_request = [
      'owner_id' => (($parsedInfo['response']['type']==='user')?'':'-').$parsedInfo['response']['object_id'],
      'message' => $_POST['message'],
      'access_token' => $access_token,
      'v' => '5.102',
    ];

    if (isset($_POST['attachments'])) {
      $vk_post_request['attachments'] = $_POST['attachments'];
    }

    $postResponse = HTTPPost('https://api.vk.com/method/wall.post', $vk_post_request);
    $parsedPostResponse = json_decode($postResponse, true);
    sleep(1);
  }
  echo 'VK spam completed SUCCESSFULLY! <br> <a href="/spammer.php">Go back!</a> ';
  exit;
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Spam VK walls</title>
  <style>
    #video-list {
      display: flex;
      overflow: auto;
    }
    .video-item.selected{
      border: 3px solid;
    }
    [name="message"] {
      width: 90vw;
      height: 40vh;
    }
    [name="groups"] {
      width: 90vw;
      height: 10vh;
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</head>
<body>

<form method="post" action="/spammer.php">
  <input type="hidden" name="action" value="start_spam">
  <div>Groups: (fill through space just URL-domain, example:<strong>public83001541 public83001542</strong>)</div>
  <div>
    <textarea name="groups" required></textarea>
  </div>
  <div>Text of Your Post</div>
  <div>
    <textarea name="message" required></textarea>
  </div>
  <div>Attachment video (click on video icon to include)</div>
  <div>
    <input type="text" name="attachments">
  </div>
  <div id="video-list">
  </div>
  <div><button type="button" onclick="submitForm()">SPAM!</button></div>
</form>
<hr>
Author: <a href="https://www.youtube.com/channel/UCXnbTnQDe3v6RR6LXAv6nBg">Hey, Alex!</a>

<script>
  function submitForm(element) {
    if (!$('[name="attachments"]').val()) {
      if (!confirm('Video is not attached. Continue?')) return;
    }

    const uniqueGroups = [];
    $('[name="groups"]')
      .val()
      .split(' ')
      .filter((group) => !!group)
      .forEach((group) => {
        if (!uniqueGroups.includes(group)) uniqueGroups.push(group)
      });
    console.log(uniqueGroups.join(' '), ' will sent to those groups');
    $('#video-list').hide();
    $('button').hide();
    $.ajax( {
      url: '/spammer.php',
      method: 'POST',
      data: {
        start_spam: 1,
        attachments: $('[name="attachments"]').val(),
        message: $('[name="message"]').val(),
        groups: uniqueGroups.join(' '),
      },
    }).done(function( data ) {
      $('#video-list').show();
      $('button').show();
      alert('Spammed ' + new Date());
    });
  }
  function chooseVideo(element) {
    $('[name="attachments"]').val(element.id);
    localStorage.setItem('spamVideoAttachments', element.id);
    $('.video-item').removeClass('selected');
    $('#' + element.id).addClass('selected');
  }

  $(document).ready(() => {
    $('[name="attachments"]')
      .val(localStorage.getItem('spamVideoAttachments'));
    $('[name="message"]')
      .val(localStorage.getItem('spamVideoText'))
      .keyup(() => {
        localStorage.setItem('spamVideoText', $('[name="message"]').val());
      });
    $('[name="groups"]')
      .val(localStorage.getItem('spamVideoGroups'))
      .keyup(() => {
        localStorage.setItem('spamVideoGroups', $('[name="groups"]').val());
      });


    $.ajax( {
      url: '/spammer.php?my_videos=1',
    }).done(function( data ) {
      data.response.items.forEach((item) => {
        const videoItem = $(`
        <div class="video-item" onclick="chooseVideo(this)" id="video${item.owner_id}_${item.id}">
          <img src="${item.image[0].url}" alt="item.title">
        </div>
      `);

        $('#video-list').append(videoItem);
      })
    });
  });
</script>
</body>
</html>

