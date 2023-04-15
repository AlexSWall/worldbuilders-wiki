#!/usr/bin/env bash

cd /app/web/frontend || exit 1

npm install

npm run build > /dev/null || echo 'Finished `npm run build` with non-zero exit code'

/app/dev/watch.sh
