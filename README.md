# Ibexa Platform Enterprise Edition

[![Latest release](https://img.shields.io/github/release/ezsystems/ezplatform-ee.svg?style=flat-square)](https://github.com/ezsystems/ezplatform-ee/releases)
[![License](https://img.shields.io/packagist/l/ezsystems/ezplatform-ee.svg?style=flat-square)](LICENSE)

## What is eZ Platform Enterprise Edition?
*eZ Platform Enterprise Edition* is commercial CMS (Content Management System) software developed by eZ Systems.

*eZ Platform Enterprise Edition* derives from *[eZ Platform](https://github.com/ezsystems/ezplatform)*. It is composed of a set of bundles. eZ Platform Enterprise Edition, like eZ Platform, is built on top of the Symfony framework (Full Stack). It has been in development since 2014.

### How to get access to eZ Platform Enterprise Edition?

While this meta repository, `ezplatform-ee`, is public to ease installation and upgrades, full access can be obtained in one of three ways:
- Request an online demo on [ez.no](https://ez.no/Products/eZ-Platform-Enterprise-Edition)
- As a partner, download trial version from [Partner Portal](http://ez.no/Partner-Portal)
- As a customer with an eZ Enterprise subscription, get full version from [Service Portal](https://support.ez.no/Downloads).
  Or by setting up [Composer Authentication Tokens](https://doc.ez.no/display/DEVELOPER/Using+Composer) for use in combination with this repository.

### eZ Platform Enterprise Edition vs. eZ Platform
[eZ Platform Enterprise Edition](https://ez.no/Products/eZ-Platform-Enterprise-Edition) is a distribution flavor of [eZ Platform](http://ezplatform.com/), our Symfony-based enterprise level open source CMS.
In short, Enterprise comes with addtional features and services that extend eZ Platform functionality for media industry and team collaboration.


#### Further information:
Ibexa Platform is 100% open source and is the foundation for the commercial *Ibexa Digital Experience Platform* software which adds advanced features for editorial teams, 100% built on top of *Ibexa Platform* APIs.

- [Ibexa products roadmap](https://portal.productboard.com/ibexa/1-ibexa-dxp)
- Ibexa (commercial products and services): [ibexa.co](https://ibexa.co/)

## Installation

NOTE: *For simplified installation, consider using community supported [eZ Launchpad](https://ezsystems.github.io/launchpad/) which takes care of the whole server setup for you.*

Installation instructions below are for installing a clean installation of eZ Platform Enterprise Edition in latest version with _no_ demo content or demo website.
Full installation documentation is [in the online docs](https://doc.ezplatform.com/en/latest/getting_started/install_using_composer/).
It includes instructions on installing other distributions _(like [ezplatform "clean"](https://github.com/ezsystems/ezplatform) and [ezplatform-ee-demo](https://github.com/ezsystems/ezplatform-ee-demo) enterprise edition)_, or other versions.

#### Prerequisites

These instructions assume you have already installed:
- PHP _(7.3 or higher)_
- Web Server _(Recommended: Apache / Nginx. Use of PHP's built-in development server is also possible)_
- Database server _(MySQL 5.5+ or MariaDB 10.0+)_
- [Composer](https://doc.ibexa.co/en/latest/getting_started/install_ez_platform/#get-composer)
- Git _(for development)_

For further information [on requirements see online doc](https://doc.ibexa.co/en/latest/getting_started/requirements/).


#### Install Ibexa Platform _(clean enterprise distribution)_

Assuming you have prerequisites sorted out, you can get the install up and running with the following commands in your terminal:

``` bash
composer create-project --keep-vcs ezsystems/ezplatform-ee ezplatform ^3
cd ezplatform
```

_Note: If composer is installed locally instead of globally, the first command will start with `php composer.phar`._

You must add your database connection credentials (hostname, login, password) to the environment file.  
To do this, in the main project directory, the `.env` file, change the parameters that are prefixed with `DATABASE_` as necessary.
Store the database credentials in your `.env.local` file. Do not commit the file to the Version Control System.

Use the following command to install Ibexa Platform (insert base data into the database):

```bash
composer ezplatform-install
```

**Tip:** For a more complete and better performing setup using Apache or Nginx, see how to [install Ibexa Platform manually](https://doc.ibexa.co/en/latest/getting_started/install_ez_platform/).

## Issue tracker
Submitting bugs, improvements and stories is possible on https://jira.ez.no/browse/EZEE.
If you discover a security issue, please see how to responsibly report such issues on https://doc.ez.no/Security.

## Backwards compatibility
Ibexa Platform aims to be **100% content compatible** with eZ Publish 5.x, 4.x and 3.x *(introduced in 2002)*, meaning that content in those versions of the CMS can be upgraded using
[online documentation](http://doc.ez.no/eZ-Publish/Upgrading) to Ibexa Platform.


## COPYRIGHT
Copyright (C) 1999-2020 Ibexa AS. All rights reserved.

## LICENSE
- http://ez.no/Products/About-our-Software/Licenses-and-agreements/eZ-Business-Use-License-Agreement-eZ-BUL-Version-2.1 eZ Business Use License Agreement eZ BUL Version 2.1
- https://ez.no/About-our-Software/Licenses-and-agreements/eZ-Trial-and-Test-License-Agreement-eZ-TTL-v2.0 eZ Trial and Test License Agreement (eZ TTL) v2.0
