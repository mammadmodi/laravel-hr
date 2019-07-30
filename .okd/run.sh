#!/bin/bash

echo "processing config map..."
oc process -f ConfigMap.yaml --ignore-unknown-parameters=true | oc apply -f -

echo "processing service..."
oc process -f Service.yaml --ignore-unknown-parameters=true | oc apply -f -

echo "processing deployment config..."
oc process -f DeploymentConfig.yaml --ignore-unknown-parameters=true | oc apply -f -

#echo "processing route..."
#oc process -f Route.yaml --param-file=.env --ignore-unknown-parameters=true | oc apply -f -
