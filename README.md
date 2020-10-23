# Ibexa Experience 

[![Latest release](https://img.shields.io/github/release/ezsystems/ezplatform-ee.svg?style=flat-square)](https://github.com/ezsystems/ezplatform-ee/releases)
[![License](https://img.shields.io/packagist/l/ezsystems/ezplatform-ee.svg?style=flat-square)](LICENSE)

## What is Ibexa Experience?
*Ibexa Experience* is commercial Digital Experience Platform (DXP) software developed by Ibexa.

*Ibexa Experience* derives from *[Ibexa Platform](https://github.com/ezsystems/ezplatform)*. It is composed of a set of bundles. Ibexa Experience, like Ibexa Platform, is built on top of the Symfony framework (Full Stack). It has been in development since 2014.

### How to get access to Ibexa Experience?

While this meta repository, `ezplatform-ee`, is public to ease installation and upgrades, full access can be obtained in one of three ways:
- Request an online demo on [ibexa.co](https://www.ibexa.co/products)
- As a partner, download trial version from [Partner Portal](https://www.ibexa.co/partner-portal)
- As a customer with an Ibexa Experience subscription, get full version from [Service Portal](https://support.ibexa.co/Downloads).
  Or by setting up [Composer Authentication Tokens](https://doc.ibexa.co/en/latest/getting_started/install_ez_platform/#set-up-authentication-tokens) for use in combination with this repository.

#### Further information:
Ibexa Platform is 100% open source and is the foundation for the commercial *Ibexa Digital Experience Platform* software which adds advanced features for editorial teams, 100% built on top of *Ibexa Platform* APIs.

- [Ibexa products roadmap](https://portal.productboard.com/ibexa/1-ibexa-dxp)
- Ibexa (commercial products and services): [ibexa.co](https://ibexa.co/)

## Installation

Installation instructions below are for installing a clean installation of Ibexa Experience in latest version with _no_ demo content or demo website.
Full installation documentation is [in the online docs](https://doc.ibexa.co/en/latest/getting_started/install_ez_platform/).

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

Use the following command to install Ibexa Experience (insert base data into the database):

```bash
composer ezplatform-install
```

**Tip:** For a more complete and better performing setup using Apache or Nginx, see how to [install Ibexa Experience manually](https://doc.ibexa.co/en/latest/getting_started/install_ez_platform/).

## Issue tracker
Submitting bugs, improvements and stories is possible on https://jira.ez.no/browse/EZEE.
If you discover a security issue, please see how to responsibly report such issues on https://www.ibexa.co/software-information/security.

## Backwards compatibility
Ibexa Experience aims to be **fully content compatible** with eZ Publish 5.x, meaning that the content in these versions of the CMS can be upgraded using
[online documentation](https://doc.ezplatform.com/en/latest/migrating/migrating_from_ez_publish_platform/) to Ibexa Experience.


## COPYRIGHT
Copyright (C) 1999-2020 Ibexa AS. All rights reserved.

## LICENSE
- https://www.ibexa.co/software-information/licenses-and-agreements/ibexa-trial-and-test-license-agreement-ibexa-ttl-v2.2 Ibexa Business Use License Agreement Ibexa BUL Version 2.3
- https://www.ibexa.co/software-information/licenses-and-agreements/ibexa-business-use-license-agreement-ibexa-bul-version-2.3 Ibexa Trial and Test License Agreement (Ibexa TTL) v2.2
