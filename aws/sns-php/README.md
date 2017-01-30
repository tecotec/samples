# Amazon SNS デモ （PHP）

## SNS/SES

Amazon SNS、SESの両方のデモが含まれます。

### htdocs/index.php  

Amazon SNS の利用サンプルです。

### htdocs/ses.php

Amazon SES の利用サンプルです。

## 認証

Amazon SNS SDKを使うには、AWSの`アクセスキーID`と`シークレットアクセスキー`という二つの認証情報が必要になる。

AWS管理画面の「セキュリティ情報」から取得できる。  

ユーザーには`AmazonSNSFullAccess`権限が必要。  
行う作業によって細かく権限を付けてもいいかもしれない。

### PHPから認証情報を使う方法

* 環境変数の資格情報を使用
* AWS資格情報ファイルと資格情報プロファイルの使用
* ハードコーディングされた資格情報を使用

他にもある模様。詳しくは以下参照。  
http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/credentials.html

本デモでは環境変数を使用している。

### 環境変数の資格情報を使用

以下の名前でアクセスキーIDとシークレットアクセスキーを環境変数に設定する。  
PHPの`getenv()`で取得できるようにすること。

```
AWS_ACCESS_KEY_ID
AWS_SECRET_ACCESS_KEY
```

### AWS資格情報ファイルと資格情報プロファイルの使用

以下の形式でテキストファイルを作成し、 `~/.aws/credentials` として保存する

```
[default]
aws_access_key_id = my-access-key-id
aws_secret_access_key = my-secret-access-key
```

※場所がわからない場合、とりあえず設置せずに実行し、エラー文言を確認する。  
エラー文言中に設置すべき場所が含まれている。

この方法を用いる場合、クライアントをインスタンス化する際に`profile`というパラメータで設定名を指定する必要がある。

```
$snsClientConfig = [
    'profile' => 'default',       // ここに credentials で設定した名前を指定
    'region' => 'ap-northeast-1',
    'version' => 'latest'
];
```

### ハードコーディングされた資格情報を使用

クライアントのインスタンス化時に`credentials`というパラメータで直接渡す。

```
$snsClientConfig = [
    'region' => 'ap-northeast-1',
    'version' => 'latest',
    'credentials' => [
        'key'    => 'my-access-key-id',
        'secret' => 'my-secret-access-key',
    ],
];
```

以下のような理由で推奨されていない。

>【警告】
>誤って資格情報をSCMリポジトリにコミットして、意図したより多くの人に資格情報を公開する可能性があるため、
>資格情報のハードコーディングは危険です。 
>また、将来の資格情報のローテーションを困難にする可能性もあります。