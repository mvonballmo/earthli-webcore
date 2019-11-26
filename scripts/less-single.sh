#!/usr/bin/env bash

FOLDER="../site/resources/styles/themes"
FILE=$1.less

lessc $FOLDER/$FILE $FOLDER/`basename $FILE .less`.css
