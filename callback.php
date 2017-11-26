<?php
  require_once("env.php"); // 環境設定ファイルの読み込み
  // アクセストークンを取得する
  // POSTヘッダを生成する
  $sendHeader = [
  	'Authorization: Basic ' . base64_encode(CLIENT_ID.':'.CLIENT_SECRET),
  	'Content-Type: application/x-www-form-urlencoded',
  ];
  // POSTパラメータを生成する
  $sendParams = array(
  	'client_id' => CLIENT_ID,
  	'grant_type' => 'authorization_code',
  	'redirect_uri' => CALLBACK_URL,
  	'code' => $_GET['code'],
  );
  // POST送信
  $sendOptions = array(
  	'http' => array(
  		'method' => 'POST',
  		'header' => implode(PHP_EOL,$sendHeader),
  		'content' => http_build_query($sendParams),
  		'ignore_errors' => true
  	)
  );
  // レスポンス
  $sendContext = stream_context_create($sendOptions);
  $sendResponse = file_get_contents(TOKEN_URL, false, $sendContext);
  $token = json_decode($sendResponse, true); // アクセストークン
  // エラー処理
  if(isset($token['error'])){
  	echo 'ERROR!!!';
  	exit;
  }
  $access_token = $token['access_token']; // アクセストークン
  // $user_id = $token['user_id']; // ユーザID


// Combination of callback.php and heartrate.php

  $header = 'Authorization: Bearer ' . $access_token; // アクセストークン
  // $params = array('access_token' => $access_token);
  $options = array(
    'http' => array(
      'method' => 'GET',
      'header' => $header,
      'ignore_errors' => true
    )
  );

  $context = stream_context_create($options); // HTTPリクエストを$api_urlに対して送信する


// Steps - Returns number of steps
	$step_url = 'https://api.fitbit.com/1/user/-/activities/steps/date/today/1m.json';

	$step_json = file_get_contents($step_url, false, $context);
	$step = json_decode($step_json, true);
	$step_len = count($step["activities-steps"]);

	$step_response = array();

	$stepTime = $step["activities-steps"][$step_len-1]["dateTime"];
	$stepValue = $step["activities-steps"][$step_len-1]["value"];

// Distance - Returns in KM
	$dist_url = 'https://api.fitbit.com/1/user/-/activities/distance/date/today/1m.json';

	$dist_json = file_get_contents($dist_url, false, $context);
	$dist = json_decode($dist_json, true);
	$dist_len = count($dist["activities-distance"]);

	$distTime = $dist["activities-distance"][$dist_len-1]["dateTime"];
	$distValue = $dist["activities-distance"][$dist_len-1]["value"];

// Body Fat - Returns Percentage
	$bf_url = 'https://api.fitbit.com/1/user/-/body/fat/date/today/1d.json';

	$bf_json = file_get_contents($bf_url, false, $context);
	$bf = json_decode($bf_json, true);
	$bf_len = count($bf["body-fat"]);

	$bfTime = $bf["body-fat"][$bf_len-1]["dateTime"];
	$bfValue = $bf["body-fat"][$bf_len-1]["value"];

// Weight - Returns in Kilos

	$wt_url = 'https://api.fitbit.com/1/user/-/body/weight/date/today/1d.json';

	$wt_json = file_get_contents($wt_url, false, $context);
	$wt = json_decode($wt_json, true);
	$wt_len = count($wt["body-weight"]);

	$wtTime = $wt["body-weight"][$wt_len-1]["dateTime"];
	$wtValue = $wt["body-weight"][$wt_len-1]["value"];

// Heart Rate - Returns in BPM

	$hr_url = 'https://api.fitbit.com/1/user/-/activities/heart/date/today/1d/1sec.json';
	
	$heartrate_json = file_get_contents($hr_url, false, $context); // レスポンス
	$heatrate = json_decode($heartrate_json, true); // 配列デコード
	$heatrate_len = count($heatrate["activities-heart-intraday"]["dataset"]);

	$hrTime = $heatrate["activities-heart-intraday"]["dataset"][$heatrate_len-1]["time"];
	$hrValue = $heatrate["activities-heart-intraday"]["dataset"][$heatrate_len-1]["value"];
?>
<html>
  <head>
    <meta charset="utf-8">
    <title>Fitbit HeartRate PHP7.0</title>
  </head>
  <body>

   <div align = "center">
   	<h2>Original Code: Fitbit Web API by PHP7 <a href="https://github.com/code-of-design/fitbit-heartrate-php">https://github.com/code-of-design/fitbit-heartrate-php</a></h2>
   	<h3>Extended: Malon Enterprises: <a href = "https://github.com/MalonEnterprises/fitbit-heartrate-php">https://github.com/MalonEnterprises/fitbit-heartrate-php</a></h3>
   </div>
   <div align="center">
    <table>
    	<th>
    		Steps
    	</th>
    	<th>
    		Distance
    	</th>
    	<th>
    		Body Fat
    	</th>
    	<th>
    		Weight
    	</th>
    	<th>
    		Heart Rate
    	</th>
    	<tr>
    		<td valign="top">
    			<b>Steps: </b><?php echo $stepValue; ?><br>
    			<b>Time: </b><?php echo $stepTime; ?>&nbsp;
    		</td>
    		<td valign="top">
    			<b>Distance (km): </b><?php echo round($distValue, 1); ?><br>
    			<b>Distance (mi): </b><?php echo round($distValue * 0.62137119, 2); ?><br>
    			<b>Time: </b><?php echo $distTime; ?>&nbsp;
    		</td>
    		<td valign="top">
    			<b>Body Fat: </b><?php echo round($bfValue, 2); ?><br>
    			<b>Time: </b><?php echo $bfTime; ?>&nbsp;
    		</td>
    		<td valign="top">
    			<b>Weight (kg): </b><?php echo round($wtValue, 2); ?><br>
    			<b>Weight (lb): </b><?php echo round($wtValue / 0.45359237, 2); ?><br>
    			<b>Time: </b><?php echo $wtTime; ?>&nbsp;
    		</td>
    		<td valign="top">
    			<b>Heart Rate: </b><?php echo $hrValue; ?><br>
    			<b>Time: </b><?php echo $hrTime; ?>&nbsp;
    		</td>
    	</tr>
    </table>
   </div>
  </body>
</html>
