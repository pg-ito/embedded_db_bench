#!/bin/bash -xe

PROC=${1:-4}
TYPE=${2:-db4}

echo "${TYPE} ${PROC} processes start"
seq 1 ${PROC}|xargs -I{} -P ${PROC} bash -c "php dba_bench_rw.php ${TYPE} r"
