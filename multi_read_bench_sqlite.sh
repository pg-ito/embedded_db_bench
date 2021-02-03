#!/bin/bash -xe

SCRIPT=${1:-sqlite_bench.php}
PROC=${2:-4}
BASE_PATH=${3:-data/}

echo "${SCRIPT} ${PROC} processes start"
seq 1 ${PROC}|xargs -I{} -P ${PROC} bash -c "php ${SCRIPT} r ${BASE_PATH}"
