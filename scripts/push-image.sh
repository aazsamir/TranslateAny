#!/bin/sh

tag="0.2"
docker build -t aazsamir/translate-any:$tag -f Dockerfile .
docker push aazsamir/translate-any:$tag
docker tag aazsamir/translate-any:$tag aazsamir/translate-any:latest
docker push aazsamir/translate-any:latest