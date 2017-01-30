# Commands:
#   hubot hello - Say "Hi"

module.exports = (robot) ->
    robot.hear /HELLO$/i, (msg) ->
        msg.send "Hi"

