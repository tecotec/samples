FROM node:6
MAINTAINER ikeda <ikeda.masaki@gmail.com>

RUN apt-get update

# install hubot genarator
RUN npm install -g yo generator-hubot

# create user
RUN useradd hubot
RUN mkdir /home/hubot && chown hubot.hubot /home/hubot

USER hubot
WORKDIR /home/hubot

# genarate hubot
RUN  yo hubot --owner "ikeda" --name "hubot-sample" --description "Hubot image" --adapter slack

ENTRYPOINT ["bin/hubot","--adapter","slack"]

