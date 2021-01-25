#!/bin/bash

# jq -s -R 'split("\n")|map(split(","))|map("id":.[0]: {"id": .[0], "prefecture": .[1], "city": .[2], "street": .[3], "rome_prefecture": .[4], "rome_city": .[5], "rome_street": .[6]})' data/KEN_ALL_ROME_utf8.CSV > data/postal_code.json

jq -s -R 'split("\n")|map(split(",")|map(gsub("\"";"")))|map({"id": .[0], "prefecture": .[1], "city": .[2], "street": .[3], "rome_prefecture": .[4], "rome_city": .[5], "rome_street": .[6]})' data/KEN_ALL_ROME_utf8.CSV > data/postal_code.json

