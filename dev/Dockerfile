FROM node:14

ENV NODE_ENV=development

RUN apt-get update \
	&& apt-get install inotify-tools expect ruby-full -y \
	&& npm install -g npm@7 \
	&& npm install -g webpack webpack-cli wikipeg \
	&& gem install sass compass

CMD ["/app/dev/run-dev.sh"]
