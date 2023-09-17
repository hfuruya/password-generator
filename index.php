<?php

// TODO
// ・テキストボックスやめたい（スマホだとテキストパネルが出てきて閉じられる。ただのテキストとか無理か？）

/*
 * 定義
 */
const NUM = 1;
const LIMIT_NUM = 50;
const LENGTH = 8;
const LIMIT_LENGTH = 128;
const NUMBER = true;
const UPPER_ALPHABET = false;
const PREFIX_UPPER_ALPHABET = false;
const LOWER_ALPHABET = true;
const SYMBOL = false;

$ret = [];
$msg = "";

/*
 * パスワード生成
 * 
 * return array
 */
function generateRandomStr($num=NUM, $length=LENGTH, $symbol=SYMBOL, $number=NUMBER, $upperAlphabet=UPPER_ALPHABET, $lowerAlphabet=LOWER_ALPHABET, $prefixUpperAlphabet=PREFIX_UPPER_ALPHABET)
{
	$fixBaseString = '';
	$ary = [];

	// ランダム文字列生成（記号・数字・英大小文字）
	if ($symbol) $fixBaseString .= getSymbol();
	if ($number) $fixBaseString .= getNumber();
	if ($upperAlphabet) $fixBaseString .= getUpperCaseAlphabet();
	if ($lowerAlphabet) $fixBaseString .= getLowerCaseAlphabet();

	// Validation
	// ・空文字の場合は生成しない
	if ($fixBaseString === "") return $ary;

	for ($i=0; $i<$num; ++$i)
	{
		// 先頭が英大文字固定の対応
		if ($prefixUpperAlphabet)
		{
			$ary[$i] = substr(str_shuffle(getUpperCaseAlphabet()), 0, 1) . substr(str_shuffle(str_repeat($fixBaseString, $length)), 1, $length-1);
		}
		// 通常時
		else
		{
			$ary[] = substr(str_shuffle(str_repeat($fixBaseString, $length)), 0, $length);
		}
	}

	return $ary;
}
// ランダム文字列生成に必要な要素を取得する
function getNumber () { return '0123456789'; }
function getLowerCaseAlphabet () { return 'abcdefghijklmnopqrstuvwxyz'; }
function getUpperCaseAlphabet () { return strtoupper(getLowerCaseAlphabet()); }
function getSymbol () { return "!#$%&()=~|<>,./+*:[]{}^-;"; } // 含めていないもの(¥, ", ') .. エスケープされて文字数が少なくなる等の為

// パラメータをセット
// ・文字列型の数値は数値へキャストする
$action = isset($_POST['action'])? $_POST['action']:null;
$num = isset($_POST['num'])? (int)$_POST['num']:NUM;
$length = isset($_POST['length'])? (int)$_POST['length']:LENGTH;
$symbol = isset($_POST['symbolCheck'])?? SYMBOL;
$number = isset($_POST['numberCheck'])?? NUMBER;
$upperAlphabet = isset($_POST['upperAlphabetCheck'])?? UPPER_ALPHABET;
$prefixUpperAlphabet = isset($_POST['prefixUpperCheck'])?? PREFIX_UPPER_ALPHABET;
$lowerAlphabet = isset($_POST['lowerAlphabetCheck'])?? LOWER_ALPHABET;

// Validation
if ($length > LIMIT_LENGTH) $length = LIMIT_LENGTH;
if ($length <= 0) $length = LENGTH;
if ($num > LIMIT_NUM) $num = LIMIT_NUM;
if ($num <= 0) $num = NUM;

// 実行
switch ($action)
{
	case "generate":
		$ret = generateRandomStr($num, $length, $symbol, $number, $upperAlphabet, $lowerAlphabet, $prefixUpperAlphabet);
		if (!empty($ret)) {
			$msg = "ランダム文字列を生成しました<br><br>";
			$msg .= "* 文字数 : " . $length;
			$msg .= " / 個数 : " . $num;
			$msg .= " /  数字 : " . ($number? "○":"×");
			$msg .= " /  英大文字 : " . ($upperAlphabet? "○":"×") . " ※先頭英大文字固定 : ". ($prefixUpperAlphabet? "○":"×");
			$msg .= " /  英小文字 : " . ($lowerAlphabet? "○":"×");
			$msg .= " /  記号 : " . ($symbol? "○":"×");
		} else {
			$msg = "ランダム文字列の生成に失敗しました";
		}
		break;
	default:
		break;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Password Generator</title>

	<link rel="icon" href="img/favicon.ico">
	<link rel="apple-touch-icon" href="img/apple-touch-icon-152x152.png" sizes="152x152">
	<!-- <link rel="apple-touch-icon" type="image/png" href="/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" href="/icon-192x192.png"> -->

	<link href="css/bootstrap.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>
<body>
	<style>
		body {
			margin: 0 auto;
		}
		input#length, input#num {
			width: 100px;
		}
	</style>
	<script>
		function copyToClipboard(id) {
			var _id = id;
			var copyTarget = document.getElementById(_id);
			copyTarget.select();
			document.execCommand("Copy");
			alert("コピーしました : " + copyTarget.value);
			// focusを外す
			copyTarget.blur();
		}

		$(function(){
			$('input').on('click', function(e){
				// 対象のinput以外は除外
				if (e.currentTarget.className !== "pass-phrase") return;
				copyToClipboard(e.currentTarget.id)
			});
		});
	</script>

	<h1><a href="">Password Generator</a></h1>

	<!-- <button type="button" class="btn btn-default">Default</button>
	<button type="button" class="btn btn-primary">Primary</button>
	<button type="button" class="btn btn-success">Success</button>
	<button type="button" class="btn btn-info">Info</button>
	<button type="button" class="btn btn-warning">Warning</button>
	<button type="button" class="btn btn-danger">Danger</button>
	<button type="button" class="btn btn-link">Link</button> -->

	<hr>

	<?php if (!empty($msg)) { ?>
		<?php if (!empty($ret)) { ?>
	<div class="alert alert-success" role="alert"><?php echo $msg ?></div>
		<?php } else { ?>
	<div class="alert alert-danger" role="alert"><?php echo $msg ?></div>
		<?php } ?>
	<?php } ?>

	<h2>条件</h2>
	<form style="margin: 20px 40px;" action="" method="post">
		<div class="form-group">
			<label for="length">文字数</label>
			<input type="number" class="form-control" name="length" id="length" placeholder="文字数" value="<?php echo $length; ?>" min="1" max="<?php echo LIMIT_LENGTH ?>" required>
			<label for="num">個数</label>
			<input type="number" class="form-control" name="num" id="num" placeholder="個数" value="<?php echo $num; ?>" min="1" max="<?php echo LIMIT_NUM ?>" required>
		</div>
		<div class="form-group">
			<input class="form-check-input" type="checkbox" name="numberCheck" id="numberCheck" <?php echo $number? 'checked':''; ?>>
			<label class="form-check-label" for="numberCheck">数字</label>
		</div>
		<div class="form-group">
			<input class="form-check-input" type="checkbox" name="upperAlphabetCheck" id="upperAlphabetCheck" <?php echo $upperAlphabet? 'checked':''; ?>>
			<label class="form-check-label" for="upperAlphabetCheck">英大文字</label>
			<div class="form-group form-check">
				<input class="form-check-input" type="checkbox" name="prefixUpperCheck" id="prefixUpperCheck" <?php echo $prefixUpperAlphabet? 'checked':''; ?>>
				<label class="form-check-label" for="prefixUpperCheck">先頭を英大文字固定</label>
			</div>
  		</div>
		<div class="form-group">
			<input class="form-check-input" type="checkbox" name="lowerAlphabetCheck" id="lowerAlphabetCheck" <?php echo $lowerAlphabet? 'checked':''; ?>>
			<label class="form-check-label" for="lowerAlphabetCheck">英小文字</label>
		</div>
		<div class="form-group">
			<input class="form-check-input" type="checkbox" name="symbolCheck" id="symbolCheck" <?php echo $symbol? 'checked':''; ?>>
			<label class="form-check-label" for="symbolCheck">記号</label>
		</div>
		
		<input type="hidden" name="action" value="generate">
		<button type="submit" class="btn btn-primary">生成</button>
	</form>

	<?php if (!empty($ret)) { ?>
		<h2>生成結果</h2>
		<div style="margin: 40px;">
			<?php foreach ($ret as $data) { ?>
				<input id="<?php echo $data ?>" type="text" class="pass-phrase" value="<?php echo $data ?>">
			<?php } ?>
		</div>
	<?php } ?>

</body>
</html>
