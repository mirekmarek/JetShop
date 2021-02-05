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

rm -f sites/admin/site_data.php
rm -f sites/rest/site_data.php
rm -f sites/web/site_data.php

rm -rf public/css_packages
rm -rf public/js_packages
rm -rf public/test_uploads
rm -rf public/imagegallery
