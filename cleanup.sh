#!/bin/bash

rm -f application/data/_jet_studio_access.php
rm -f application/data/activated_modules_list.php
rm -f application/data/installed.txt
rm -f application/data/installed_modules_list.php
rm -f application/config/data_model.php
rm -f application/config/db.php
rm -f application/config/mailing.php

rm -f logs/*
rm -rf tmp/*
rm -rf cache/*
rm -rf images/*
rm -rf files/*
rm -rf application/data/*

rm -f sites/admin/site_data.php
rm -f sites/rest/site_data.php
rm -f sites/web/site_data.php

rm -rf css/packages
rm -rf js/packages
