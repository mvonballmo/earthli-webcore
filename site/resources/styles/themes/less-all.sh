for file in *.less; do lessc $file `basename $file .less`.css ; done
