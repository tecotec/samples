<?php
require_once __DIR__ . '/../vendor/autoload.php';

//設定値（運用時はアプリケーションの設定ファイルで管理推奨）
$platformApplicationArn = 'arn:aws:sns:ap-northeast-1:889618090367:app/GCM/sns_test';
$snsClientConfig = [
    'region' => 'ap-northeast-1',
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

//SNSクライアントインスタンス化
$client = new Aws\Sns\SnsClient($snsClientConfig);

//画面表示制御用変数
$defaults = [
    'targetArn' => '',
    'token' => '',
    'message' => ''
];

//メッセージ送信=================================================================
if ($_POST['Publish']) {
    try {
        $message = $_POST['message'];
        $targetArn = $_POST['TargetArn'];
        $msg = [
            'Message' => $message,
            'TargetArn' => $targetArn,
        ];
        $client->publish($msg);
    } catch (Aws\Sns\Exception\SnsException $e) {
        echo $e->getMessage();
        exit;
    }

    $defaults['targetArn'] = $_POST['TargetArn'];
    $defaults['message'] = $_POST['message'];
}

//エンドポイント作成=================================================================
if ($_POST['CreatePlatformEndpoint']) {
    try {
        $token = $_POST['token'];
        $params = [
            'PlatformApplicationArn' => $platformApplicationArn,
            'Token' => $token,
        ];
        $client->createPlatformEndpoint($params);
    } catch (Aws\Sns\Exception\SnsException $e) {
        echo $e->getMessage();
        exit;
    }

    $defaults['token'] = $_POST['token'];
}

//エンドポイント削除=================================================================
if ($_POST['DeleteEndpoint']) {
    try {
        $endpointArn = $_POST['EndpointArn'];
        $params = [
            'EndpointArn' => $endpointArn,
        ];
        $client->deleteEndpoint($params);
    } catch (Aws\Sns\Exception\SnsException $e) {
        echo $e->getMessage();
        exit;
    }
}

//アンサブスクライブ=================================================================
if ($_POST['Unsubscribe']) {
    try {
        $subscriptionArn = $_POST['subscriptionArn'];
        $params = [
            'SubscriptionArn' => $subscriptionArn
        ];
        $result = $client->unsubscribe($params);
        echo '<pre>';var_dump($result);echo '</pre>';
    } catch (Aws\Sns\Exception\SnsException $e) {
        echo $e->getMessage();
        exit;
    }
}

//サブスクライブ=================================================================
if ($_POST['Subscribe']) {
    try {
        $endpointArn = $_POST['EndpointArn'];
        $topicArn = $_POST['topicArn'];
        $params = [
            'Endpoint' => $endpointArn,
            'Protocol' => 'application',
            'TopicArn' => $topicArn
        ];
        $client->subscribe($params);
    } catch (Aws\Sns\Exception\SnsException $e) {
        echo $e->getMessage();
        exit;
    }

    $defaults['token'] = $_POST['token'];
}

//トピックの作成=================================================================
if ($_POST['CreateTopic']) {
    try {
        $name = $_POST['Name'];
        $params = [
            'Name' => $name,
        ];
        $client->createTopic($params);
    } catch (Aws\Sns\Exception\SnsException $e) {
        echo $e->getMessage();
        exit;
    }

    $defaults['token'] = $_POST['token'];
}

//トピックスに紐づいたサブスクリプションの確認=================================================================
$subscriptionsByTopic = null;
$topicArn = '';
if ($_GET['ListSubscriptionsByTopic']) {
    try {
        $topicArn = $_GET['ListSubscriptionsByTopic'];
        $params = [
            'TopicArn' => $topicArn
        ];
        $subscriptionsByTopic = $client->listSubscriptionsByTopic($params);
    } catch (Aws\Sns\Exception\SnsException $e) {
        echo $e->getMessage();
        exit;
    }
}

//トピック一覧取得=================================================================
$topics = $client->listTopics();

//エンドポイント一覧取得=================================================================
$endpoints = $client->listEndpointsByPlatformApplication([
    'PlatformApplicationArn' => $platformApplicationArn,
]);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amazon SNS Demo</title>

    <!-- bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body style="padding-bottom:30px;">
    <div class="container">
        <h1>Amazon SNS Demo</h1>

        <h2 class="page-header">Publish</h2>
        <p>
            メッセージを送信する。EndpointArnを指定すれば個別に、TopicArnを指定すればTopicにサブスクライブされたエンドポイントにまとめて送信できる。
        </p>
        <form method="post">
            <div class="form-group">
                <label for="TargetArn">TargetArn</label>
                <input type="text" name="TargetArn" id="TargetArn" value="<?=$defaults['targetArn']?>" title="TargetArn" class="form-control">
            </div>
            <div class="form-group">
                <label for="message">message</label>
                <input type="text" name="message" id="message" value="<?=$defaults['message']?>" title="message" class="form-control">
            </div>
            <input type="submit" value="Publish" name="Publish" class="btn btn-primary">
        </form>

        <h2 class="page-header">ListEndpointsByPlatformApplication</h2>
        <p>
            アプリケーションに登録されたエンドポイントの一覧。100件ずつ取得可能。このサンプルでは最初の100件のみ表示。
        </p>
        <dl class="dl-horizontal">
            <dt>DeleteEndpoint</dt>
            <dd>エンドポイントを削除する。</dd>
        </dl>
        <table class="table table-bordered">
            <tr class="table-header">
                <th rowspan="2">Actions</th>
                <th rowspan="2">EndpointArn</th>
                <th colspan="2">Attributes</th>
            </tr>
            <tr>
                <th>Enabled</th>
                <th>Token</th>
            </tr>
            <?php foreach ($endpoints['Endpoints'] as $endpoint) : ?>
                <tr>
                    <td>
                        <form method="post">
                            <input type="hidden" name="EndpointArn" value="<?=$endpoint['EndpointArn']?>">
                            <input type="submit" name="DeleteEndpoint" value="DeleteEndpoint" class="btn btn-warning">
                        </form>
                    </td>
                    <td><input class="form-control" value="<?=$endpoint['EndpointArn']?>" disabled></td>
                    <td><?=$endpoint['Attributes']['Enabled']?></td>
                    <td><input class="form-control" value="<?=$endpoint['Attributes']['Token']?>" disabled></td>
                </tr>
            <?php endforeach ?>
        </table>

        <h2 class="page-header">CreatePlatformEndpoint</h2>
        <p>
            デバイストークン、登録IDをアプリケーションに登録する。登録済みだった場合は何も起こらない。現状Androidにのみ対応。
        </p>
        <form method="post">
            <div class="form-group">
                <label for="token">token</label>
                <input type="text" name="token" id="token" value="<?=$defaults['token']?>" title="token" class="form-control">
            </div>
            <input type="submit" value="Create" name="CreatePlatformEndpoint" class="btn btn-primary">
        </form>

        <h2 class="page-header">ListTopics</h2>
        <p>
            トピックスの一覧。100件ずつ取得可能。このサンプルでは最初の100件のみ表示。
        </p>
        <dl class="dl-horizontal">
            <dt style="width: 200px;">ListSubscriptionsByTopic</dt>
            <dd style="margin-left: 220px;">トピックに紐づいたサブスクリプション（エンドポイント）の一覧を表示する。</dd>
        </dl>
        <table class="table table-bordered">
            <tr>
                <th>Actions</th>
                <th>TopicArn</th>
            </tr>
            <?php foreach ($topics['Topics'] as $topic) : ?>
                <tr>
                    <td>
                        <a href="?ListSubscriptionsByTopic=<?=urlencode($topic['TopicArn'])?>" class="btn btn-info">ListSubscriptionsByTopic</a>
                    </td>
                    <td><input class="form-control" value="<?=$topic['TopicArn']?>" disabled></td>
                </tr>
            <?php endforeach ?>
        </table>

        <h2 class="page-header">ListSubscriptionsByTopic</h2>
        <p>
            トピックに紐づいたサブスクリプション（エンドポイント）の一覧。100件ずつ取得可能。このサンプルでは最初の100件のみ表示。
        </p>
        <dl class="dl-horizontal">
            <dt>Unsubscribe</dt>
            <dd>トピックのサブスクライブを解除する。</dd>
        </dl>
        <?php if ($subscriptionsByTopic) : ?>
            <p class="text-info">target topic : <?=$topicArn?></p>
            <table class="table table-bordered">
                <tr>
                    <th>Actions</th>
                    <th>SubscriptionArn</th>
                    <th>Owner</th>
                    <th>Protocol</th>
                    <th>Endpoint</th>
                </tr>
                <?php foreach ($subscriptionsByTopic['Subscriptions'] as $subscription) : ?>
                    <tr>
                        <td>
                            <form method="post">
                                <input type="hidden" name="subscriptionArn" value="<?=$subscription['SubscriptionArn']?>">
                                <input type="submit" class="btn btn-warning" name="Unsubscribe" value="Unsubscribe">
                            </form>
                        </td>
                        <td><input class="form-control" value="<?=$subscription['SubscriptionArn']?>" disabled></td>
                        <td><?=$subscription['Owner']?></td>
                        <td><?=$subscription['Protocol']?></td>
                        <td><input class="form-control" value="<?=$subscription['Endpoint']?>" disabled></td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php else : ?>
            <p>
                Topic一覧で「ListSubscriptionsByTopic」をクリックすると表示される。
            </p>
        <?php endif ?>

        <h2 class="page-header">CreateTopic</h2>
        <p>
            トピックを新規作成する。
        </p>
        <form method="post">
            <div class="form-group">
                <label for="Name">Name</label>
                <input type="text" name="Name" id="Name" value="" title="Name" class="form-control" aria-describedby="topicNameHelp">
                <span id="topicNameHelp" class="help-block">トピック名は大文字小文字のASCII文字、数字、アンダースコア、ハイフンのみ利用可能で、1～256文字まで。</span>
            </div>
            <input type="submit" value="CreateTopic" name="CreateTopic" class="btn btn-primary">
        </form>

        <h2 class="page-header">Subscribe</h2>
        <p>
            エンドポイントをトピックに登録する。既に登録済みの場合何も起こらない。
        </p>
        <form method="post">
            <div class="form-group">
                <label for="EndpointArn">EndpointArn</label>
                <input type="text" name="EndpointArn" id="EndpointArn" value="<?=$defaults['endpointArn']?>" title="EndpointArn" class="form-control">
            </div>
            <div class="form-group">
                <label for="topicArn">topicArn</label>
                <input type="text" name="topicArn" id="topicArn" value="<?=$defaults['topicArn']?>" title="topicArn" class="form-control">
            </div>
            <input type="submit" value="Subscribe" name="Subscribe" class="btn btn-primary">
        </form>
    </div>
</body>
</html>