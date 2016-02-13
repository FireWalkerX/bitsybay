The BitsyBay Project
====================

BitsyBay is an experimental minimalistic service to help you buy or sell digital creative with cryptocurrency like BitCoin. It includes marketplace for legal CMS extensions, illustrations, photos, themes and other creative assets from them authors.  
It was made by and for creative peoples who love a freedom and looking to easy trading around the world.  

BitsyBay Engine
===============

We believe that liberty service should be accessible to its users: it's 100% open source licensed under the GNU GPL version 3.  
If you know how to make this world better, join to the party or use this engine as your own BitCoin service.  

DONATE
------

By donating to the foundation, you are helping us fund and manage this project: 

    BTC 13t5kVqpFgKzPBLYtRNShSU2dMSTP4wQYx

STATUS
------

Open Beta


FEATURES
--------

* Unlimited Categories
* Unlimited Products
* Unlimited Users
* Multi-currency
* Multi-language

**Account**

* User verification
* File quota for each seller
* Basic brute force protection
* Profile page and account settings
* Global notification system
* Email notifications
* Affiliate program
* Inviting features

**Catalog**

* SEF support
* Auto redirects 301 from old to new url
* Product Reviews
* Product Demos
* Product Videos
* Product Audios
* Product Specials
* Product Favorites
* Product Tags
* Flexible Licenses
* Morphological Search based on Sphinx
* Search requests logging
* Comment form
* AJAX file uploading
* Automatic image generation based on IdentIcon algorithm
* Automatic image resizing
* Optional watermarking
* Abuse reporting

**Analytics**

* Search log
* Security log
* 404 log

**Payment**

* NYOP (Name-Your-Own-Price ) business model
* Royalty-Free/Exclusive order implementation
* Standalone BitCoin payment processor

TODO LIST
---------

* Last viewed list
* Pagination (autoload and page-by-page)
* Find-by-color filter (images indexing already implemented)
* Filter by tags (a.k.a. rubrics in the current architecture)

COMING SOON
-----------

* Free offers (with donation interface)
* Multi-currency implementation

REQUIREMENTS
------------


    apache2 
    php5 
    mysql-server  
    php-gd 
    php-imagick 
    php-curl
    curl
    bitcoind
    sphinxsearch
    ffmpeg
    clamav, php-clamav

INSTALL
-------

* Copy all content from **/upload** directory to your host root directory
* Change **/pulic** directory as public root directory
* Enable rewrite module
* Create the database from the dump **/database/structure.sql**
* Import custom database content from the dump **/database/data/*.sql**
* Rename **/upload/config-default.php** to **/upload/config.php**
* Change your settings in the **/upload/config.php** file
* Set write-access to the following directories:


    /storage  
    /public/image/cache  
    /public/audio/cache  
    /public/video/cache  
    /system/log  

* Setup crontab for automatic order processing:


    curl --silent --request GET 'https://yourdomain/index.php?route=cron/manager/order' > /dev/null 2>&1
    
* Setup crontab for search indexation (Debian-based example):


    /usr/bin/indexer --rotate --config /etc/sphinxsearch/sphinx.conf --all > /dev/null 2>&1
   
   
* You may use additional manual/crontab tools


    /tool/cache/sitemap.php      - Static sitemap generator
    /tool/email/*                - Utils for mass mailing
    /tool/inspector/ditcoind.php - Email alert when bitcoin daemon isn't running
    /tool/inspector/error.php    - Email alert when security log isn't empty
    /tool/manager/language/*     - Batch translation utils, for example when you provide new localization

* Also do not forget about limits:


    upload_max_filesize  
    post_max_size  
    memory_limit  
    allow_url_fopen  
