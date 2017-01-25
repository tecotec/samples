# Hubot for Slack

## 単体のコンテナで使用する場合

### build

```
$ docker build -t hubot .
```

### run

```
$ docker run -itd -v /etc/localtime:/etc/localtime:ro -e HUBOT_SLACK_TOKEN=<token> hubot
```

## Docker Compose を使用する場合

### 環境変数ファイルを作成
テンプレートをコピーして、`hubot.env`として保存

### コンテナを作成して実行

```
$ docker-compose up -d
```


