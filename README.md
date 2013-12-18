Magento Remarketing Extension
=============================

Google Remarketing Extension for Magento Community Edition or Magento Enterprise.
This extension will allow you to automatically remarket products on
Google Analytics with an Adwords campaign.  At this point, remarketing only 
applies on the product detail and cart pages.  Adwords is also notified
when a customer successfully purchases an item so that they are not 
remarketed for items that they have already bought.

# Installation

The installation process is similar to most Magento extensions:

* Log into your magento backend and disable Compilation if enabled (Tools > Compilation)
* Add all of the files in the package (specifically the app folder) to your Magento store; make sure that you do NOT replace the app directory; these files should be merged.  (Use `cp -r` if working from the command line , or merge files if using FTP)
* Enable Compilation (if desired)
* Set your campaign_id.  The remarketing script will not appear on your pages until you do so.  This is set under Configuration --> Sales --> Google API --> Google Remarketing
* Clear your cache

# Changelog

0.9
---
* Initial Build


#Copyright & Warranty

Feel free to modify this extension as desired.  While I have made every effort to ensure compatibility and functional integrity, this extension is provided "as is," and will not be held liable for any problems this causes to your Magento build.  This extension is not warranted in any way. 

As with the installation of any extension, I strongly advise that you backup your database before activating or testing in a development environment before implementing to your production site.