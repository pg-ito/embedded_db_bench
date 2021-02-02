#!/bin/bash -xe

PROC=${1:-4}
echo "${PROC} processes start"
seq 1 ${PROC}|xargs -I{} -P ${PROC} bash -c "php dba_bench_rw.php db4 r"
