#! /bin/sh

PHP=/usr/bin/php
CURL=/usr/bin/curl
JAVA=/usr/bin/java

$CURL -sS https://getcomposer.org/installer | $PHP && \
$PHP composer.phar install && \
mkdir -p selenium && \
{
    if [ -e selenium/selenium.jar ]
    then
        true # do nothing
    else
        $CURL -o selenium/selenium.jar http://selenium.googlecode.com/files/selenium-server-standalone-2.37.0.jar
    fi
} && \
{
    if [ -e selenium/selenium.pid ]
    then
        kill -9 `cat selenium/selenium.pid`
    fi

    $JAVA -jar selenium/selenium.jar -trustAllSSLCertificates -timeout 30 >&/dev/null &
    PID=$!
    echo $PID > selenium/selenium.pid
} && \
{
    if [ ! -e configuration.yml ]
    then
        cp sample-configuration.yml configuration.yml
    fi
}