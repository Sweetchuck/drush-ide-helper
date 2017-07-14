# Drush - IDE Helper

[![Build Status](https://travis-ci.org/Cheppers/drush-ide-helper.svg?branch=master)](https://travis-ci.org/Cheppers/drush-ide-helper)
[![codecov](https://codecov.io/gh/Cheppers/drush-ide-helper/branch/master/graph/badge.svg)](https://codecov.io/gh/Cheppers/drush-ide-helper)

The `drush ide-helper-phpstorm-meta` command generates [PhpStorm Advanced Metadata](https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata) files from a running Drupal site.


## Install

1. Run `composer config 'repositories.drupal-8' 'composer' 'https://packages.drupal.org/8'`
1. Run `composer require --dev drupal/ide_helper`


## Usage

`drush ide-helper-phpstorm-meta`


## Screenshots

![Service name autocompletion](docs/images/screenshot-service-autcomplete.png)

------------

![Entity type id autocompletion](docs/images/screenshot-entity-type-autcomplete.png)

------------

![Entity type id autocompletion](docs/images/screenshot-interface.png)
