#!/bin/bash

rm -f application/config/data_model.php
rm -f application/config/db.php
rm -f application/config/mailing.php

rm -f logs/*
rm -rf tmp/*
rm -rf cache/*
rm -rf images/*
rm -rf files/*
rm -rf application/data/*

rm -f application/bases/admin/base_data.php
rm -f application/bases/eshop/base_data.php
rm -f application/bases/exports/base_data.php
rm -f application/bases/services/base_data.php

rm -rf css/packages
rm -rf js/packages
rm -rf templates/*

