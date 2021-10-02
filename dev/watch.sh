#!/bin/bash

if ! type inotifywait &>/dev/null ; then
	echo "The dependency 'inotifywait' is missing. Install the package inotify-tools (apt-get install inotify-tools)"
	exit 1
fi

function watchscss () {
	echo "Compiling SCSS..."
	unbuffer compass compile --output-style compressed --force
	echo "Compiled SCSS"
	while true
	do
		inotifywait --quiet -r -e close_write,moved_to,create sass > /dev/null
		echo "Compiling SCSS..."
		unbuffer compass compile --output-style compressed --force
		echo "Compiled SCSS"
		sleep .1
	done
}

function watchjs() {
	unbuffer npm run watch
}

function watchpegparser() {
	unbuffer echo 'Compiling PEG Parser...'
	unbuffer wikipeg Grammar.pegphp Grammar.php
	unbuffer echo 'Compiled PEG Parser'
	while true
	do
		inotifywait --quiet -e close_write,moved_to,create Grammar.pegphp > /dev/null
		unbuffer echo 'Compiling PEG Parser...'
		unbuffer wikipeg Grammar.pegphp Grammar.php
		unbuffer echo 'Compiled PEG Parser'
		sleep .1
	done
}

esc=$(printf '\033'); WHITE='[1;37m'; GREEN='[1;32m'; BLUE='[1;34m'; COLOUR='[1;36m'

echo 'Watching...'

#trap 'kill %1 %2' SIGINT

# Watch Javascript (npm run watch)
( cd /app/web/frontend/; watchjs | sed -e "s/^/${esc}${GREEN}[Webpack]${esc}${WHITE} /" ) & \

# Watch SCSS (inotifywait and compass compile)
( cd /app/web/frontend/; watchscss | sed -e "s/^/${esc}${BLUE}[Compass]${esc}${WHITE} /" ) & \

# Watch Wikitext grammar (inotifywait and wikipeg compile)
( cd /app/web/backend/app/WikitextConversion; watchpegparser | sed -e "s/^/${esc}${COLOUR}[Wikitext Parser] ${esc}${WHITE} /" ) & \

# Watch Infobox grammar (inotifywait and wikipeg compile)
( cd /app/web/backend/app/Infoboxes; watchpegparser | sed -e "s/^/${esc}${COLOUR}[Infobox Parser] ${esc}${WHITE} /" )

#trap - SIGINT
#echo
