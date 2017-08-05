# Drush - IDE Helper

[![Build Status](https://travis-ci.org/Sweetchuck/drush-ide-helper.svg?branch=8.x-1.x)](https://travis-ci.org/Sweetchuck/drush-ide-helper)
[![codecov](https://codecov.io/gh/Sweetchuck/drush-ide-helper/branch/8.x-1.x/graph/badge.svg)](https://codecov.io/gh/Sweetchuck/drush-ide-helper)

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
