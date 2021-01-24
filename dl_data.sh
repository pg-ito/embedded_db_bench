#!/bin/bash -xe

DATA_FILE="postal_code.zip"
DATA_DIR="data/"
DATA_FPATH="${DATA_DIR}${DATA_FILE}"
curl -o ${DATA_FPATH} https://www.post.japanpost.jp/zipcode/dl/roman/ken_all_rome.zip?200708 

unzip ${DATA_FPATH} -d ${DATA_DIR}

nkf -Lu -w ${DATA_DIR}KEN_ALL_ROME.CSV > ${DATA_DIR}KEN_ALL_ROME_utf8.CSV

# jq -s -R 'split("\n")|map(split(","))|map({"id": .[0], "prefecture": .[1], "city": .[2], "street": .[3], "rome_prefecture": .[4], "rome_city": .[5], "rome_street": .[6]})' data/KEN_ALL_ROME_utf8.CSV