#!/usr/bin/env bash

FOLDER="../site/resources/styles/themes"
LESS=../node_modules/less/bin/lessc

MASK="$1*"

for file in "$FOLDER"/$MASK.less; do
  ECHO "Processing $file..."
  $LESS "$file" $FOLDER/"$(basename "$file" .less)".css ;
done
