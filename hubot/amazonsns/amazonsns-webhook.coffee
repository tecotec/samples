util = require 'util'
bodyParser = require 'body-parser'
request = require 'request'

module.exports = (robot) ->
  robot.router.post '/webhook/:room', bodyParser.text(), (req, res) ->
    room = req.params.room
    type = if req.headers['x-amz-sns-message-type'] then req.headers['x-amz-sns-message-type'] else 'x-amz-sns-message-type is not found'
    body = if req.body then JSON.parse req.body else {}
    console.log type
    if type == 'SubscriptionConfirmation'
      requestSubscribe body.SubscribeURL
    else if type == 'Notification'
      sendMessage room, body
    res.send  'OK'

  requestSubscribe = (subscribeUrl) ->
    options =
      url: subscribeUrl
      method: 'GET'
    request options, subscribeCallback

  subscribeCallback = (error, response, body) ->
    console.log util.inspect body

  sendMessage = (room, body) ->
    message = body.Subject
    #message += "\r\n" + body.Message
    robot.messageRoom room, message
