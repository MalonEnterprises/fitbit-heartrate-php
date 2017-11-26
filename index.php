<?php
  require_once("env.php"); // 環境設定ファイルの読み込み

	// Fitbit Web APIの認証
	$params = array(
		'client_id' => CLIENT_ID,
		'redirect_uri' => CALLBACK_URL,
		'scope' => 'heartrate activity nutrition sleep weight', // 心拍数
		'response_type' => 'code', // レスポンスの種類
	);

  // リダイレクト
	header("Location: " . AUTH_URL . '?' . http_build_query($params));
  exit();
?>
