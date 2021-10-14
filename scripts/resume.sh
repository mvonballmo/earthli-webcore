#!/usr/bin/env bash

FOLDER="../site/resources/styles/themes"
LESS=../node_modules/less/bin/lessc

$LESS $FOLDER/resume.less $FOLDER/resume.css
	