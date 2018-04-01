FOLDER="../site/resources/styles/themes"

for file in $FOLDER/*.less; do lessc $file $FOLDER/`basename $file .less`.css ; done
