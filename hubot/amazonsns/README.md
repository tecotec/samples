# AmazonSNS HTTP 受信用 hubot スクリプト

## 使い方

- `amazonsns-webhook.cooffee`を`scripts`ディレクトリに置きます。
- 以下のように AmazonSNS の subscription を登録します。
```
Protocol : http
Endpoint : http://<your_hubot_host>:<your_hubot_port>/webhook/<room_id>
```
- Subscription IDが付与されていれば成功です。

## 注意
- メッセージの署名を検証する処理は入ってません。  
導入する場合は以下を参照してください。

http://docs.aws.amazon.com/ja_jp/sns/latest/dg/SendMessageToHttp.verify.signature.html
