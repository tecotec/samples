<?php
require_once __DIR__ . '/../vendor/autoload.php';

//設定値（運用時はアプリケーションの設定ファイルで管理推奨）
$sesClientConfig = [
    'region' => 'us-west-2',
    'version' => 'latest',

    //認証情報を環境変数から取得する場合は上記の設定のみでOK

    //「~/.aws/credentials」を使う場合は以下のように設定名を指定
    //'profile' => 'default',

    //ハードコーディングされた認証情報を使う場合は以下のように設定（非推奨）
    //'credentials' => [
    //    'key'    => 'my-access-key-id',
    //    'secret' => 'my-secret-access-key',
    //],
];

$client = new Aws\Ses\SesClient($sesClientConfig);

//メール送信===============================================================================
if ($_POST['SendEmail']) {
    $source = $_POST['Source'];
    $toAddress = $_POST['ToAddresses'];
    $toAddresses = explode(',', $toAddress);
    $subject = $_POST['Subject'];
    $body = $_POST['Body'];
    $mail = ['Destination' => ['ToAddresses' => $toAddresses], 'Message' => ['Body' => ['Text' => ['Data' => $body]],
        'Subject' => ['Data' => $subject]], 'Source' => $source];
    $result = $client->sendEmail($mail);
    echo '<pre>';
    var_dump($result);
    echo '</pre>';
}

//メールアドレス認証===============================================================================
if ($_POST['VerifyEmailIdentity']) {
    $emailAddress = $_POST['EmailAddress'];
    $param = [
        'EmailAddress' => $emailAddress
    ];
    $result = $client->verifyEmailIdentity($param);
}

//アイデンティティ削除===============================================================================
if ($_POST['DeleteIdentity']) {
    $result = $client->deleteIdentity([
        'Identity' => $_POST['Identity'],
    ]);
}

//アイデンティティ一覧取得===============================================================================
$verifiedEmailAddresses = $client->listIdentities();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amazon SES Demo</title>

    <!-- bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body style="padding-bottom:30px;">
    <div class="container">
        <h1>Amazon SES Demo</h1>

        <h2 class="page-header">SendEmail</h2>
        <p>
            Eメールを送信する。<br>
            <span class="text-warning">サンドボックス環境の場合、送信元も送信先も認証済みEメールアドレスでなければならない。</span>
        </p>
        <form method="post">
            <div class="form-group">
                <label for="Source">Source（送信元メールアドレス）</label>
                <input type="text" name="Source" id="Source" value="" class="form-control" placeholder="必須" required>
            </div>
            <div class="form-group">
                <label for="ToAddresses">To（送信先メールアドレス）</label>
                <input type="text" name="ToAddresses" id="ToAddresses" value="" class="form-control" placeholder="必須" aria-describedby="ToAddressesHelp" required>
                <!--<span id="topicNameHelp" class="help-block">カンマ区切りで複数指定可能</span>-->
            </div>
            <!--<div class="form-group">
                <label for="CcAddresses">Cc</label>
                <input type="text" name="CcAddresses" id="CcAddresses" value="" class="form-control">
            </div>
            <div class="form-group">
                <label for="BccAddresses">Bcc</label>
                <input type="text" name="BccAddresses" id="BccAddresses" value="" class="form-control">
            </div>-->
            <div class="form-group">
                <label for="Subject">Subject</label>
                <input type="text" name="Subject" id="BccAddresses" value="" class="form-control" placeholder="必須" required>
            </div>
            <div class="form-group">
                <label for="Body">Body</label>
                <textarea name="Body" id="Body" class="form-control" placeholder="必須" required></textarea>
            </div>
            <input type="submit" name="SendEmail" value="SendEmail" class="btn btn-primary">
        </form>

        <h2 class="page-header">ListIdentities</h2>
        <p>
            認証されたEメールアドレス（orドメイン）をすべて取得する。<br>
        </p>
        <dl class="dl-horizontal">
            <dt>DeleteIdentity</dt>
            <dd>Eメールアドレス（orドメイン）を削除する。</dd>
        </dl>
        <table class="table table-bordered">
            <tr>
                <th>Actions</th>
                <th>Identity</th>
            </tr>
            <?php foreach ($verifiedEmailAddresses['Identities'] as $address) : ?>
                <tr>
                    <td>
                        <form method="post">
                            <input type="hidden" name="Identity" value="<?=$address?>">
                            <input type="submit" name="DeleteIdentity" value="DeleteIdentity" class="btn btn-warning">
                        </form>
                    </td>
                    <td><input type="text" value="<?=$address?>" class="form-control" disabled></td>
                </tr>
            <?php endforeach ?>
        </table>

        <h2 class="page-header">VerifyEmailIdentity</h2>
        <p>
            メールアドレスを認証する。ここで登録したメールアドレス宛てに、認証用メールが送信される。<br>
            このアクションは1秒あたり1リクエストで抑制される。
        </p>
        <form method="post">
            <div class="form-group">
                <label for="EmailAddress">EmailAddress</label>
                <input type="input" name="EmailAddress" id="EmailAddress" placeholder="必須" class="form-control" required>
            </div>
            <input type="submit" name="VerifyEmailIdentity" value="VerifyEmailIdentity" class="btn btn-primary">
        </form>
    </div>
</body>
</html>