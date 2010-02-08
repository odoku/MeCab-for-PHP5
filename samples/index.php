<?php

require_once '../MeCab.php';

$text = "やつを追う前に言っておくッ！
おれは今やつのスタンドをほんのちょっぴりだが体験した。
い…いや…体験したというよりはまったく理解を超えていたのだが……。
あ…ありのまま、今、起こった事を話すぜ！
『おれは奴の前で階段を登っていたと思ったらいつのまにか降りていた。』
な、何を言ってるのかわからねーと思うが、
おれも何をされたのかわからなかった…。
頭がどうにかなりそうだった…。
催眠術だとか超スピードだとか、そんなチャチなもんじゃあ断じてねえ。
もっと恐ろしいものの片鱗を味わったぜ…。";

$type = 0;

if (isset($_POST['text'])) {
	// ご使用のmecabが採用している文字コードを指定して下さい。
	// 指定しない場合は、デフォルトでUTF-8がセットされます。
	MeCab::setEncode('UTF-8');
	
	// MeCabクラスを使用する前に、tmpディレクトリに書き込み権限があるかどうかを確認して下さい。
	$mecab = new MeCab();
	
	if ($_POST['type'] == 0) {
		// 引数として渡された文字列を、形態素解析します。
		$analyzed = $mecab->analyze($_POST['text']);
	} else {
		// 引数として渡された文字列を分かち書きに直します。
		$splited = $mecab->split($_POST['text']);
	}
	
	$text = $_POST['text'];
	$type = $_POST['type'];
}


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<title>MeCab for PHP5 Sample</title>
</head>
<body>
	<h1>MeCab for PHP5 Sample</h1>
	<form action="./index.php" method="post">
		<p>
			<textarea name="text" cols="60" rows="15" value=""><?php echo htmlspecialchars($text); ?></textarea><br>
			<input type="radio" name="type" value="0" id="type_analyze"<?php echo ($type == 0)? ' checked': ''; ?>><label for="type_analyze">解析</label>
			<input type="radio" name="type" value="1" id="type_split"  <?php echo ($type == 1)? ' checked': ''; ?>><label for="type_split">分かち書き</label>
		</p>
		<p><input type="submit" value="send"></p>
	</form>

<?php if (isset($analyzed)): ?>
	<table border="1" cellspacing="2" cellpadding="2">
<?php foreach ($analyzed as $word): ?>
		<tr>
<?php foreach ($word as $value): ?>
			<td><?php echo htmlspecialchars($value); ?></td>
<?php endforeach ?>
		</tr>
<?php endforeach ?>
	</table>
<?php endif ?>

<?php if (isset($splited)): ?>
	<p>
		<?php echo nl2br(htmlspecialchars($splited)); ?>
	</p>
<?php endif ?>

</body>
</html>