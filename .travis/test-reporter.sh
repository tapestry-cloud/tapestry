#!/usr/bin/env bash

# This file provides some helper functions for the use of code climates test coverage reporter
# with Travis.
#
# More information: https://docs.codeclimate.com/docs/travis-ci-test-coverage

function codeclimate-before-build() {
    if [[ "$WITH_COVERAGE" == "true" ]]; then
        curl -L https://codeclimate.com/downloads/test-reporter/test-reporter-latest-linux-amd64 > $TRAVIS_BUILD_DIR/cc-test-reporter
        chmod +x $TRAVIS_BUILD_DIR/cc-test-reporter
        $TRAVIS_BUILD_DIR/cc-test-reporter before-build
    fi
}

function codeclimate-after-build() {
    if [[ "$WITH_COVERAGE" == "true" ]]; then
        if [ "$TRAVIS_PULL_REQUEST" == "false" ]; then
            $TRAVIS_BUILD_DIR/cc-test-reporter after-build --exit-code $TRAVIS_TEST_RESULT
        fi
    fi
}