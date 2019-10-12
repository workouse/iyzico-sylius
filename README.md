<h1 align="center">Iyzico Payment Gateway Plugin</h1>
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/workouse/iyzico-sylius/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/workouse/iyzico-sylius/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/workouse/iyzico-sylius/badges/build.png?b=master)](https://scrutinizer-ci.com/g/workouse/iyzico-sylius/build-status/master)
<p align="center">Gateway plugin for sylius</p>

## Installation

1. Run `composer require eresbiotech/iyzico-sylius`.
2. -wip-

## Usage

### Running plugin tests

  - PHPUnit

    ```bash
    $ vendor/bin/phpunit
    ```

  - PHPSpec

    ```bash
    $ vendor/bin/phpspec run
    ```

  - Behat (non-JS scenarios)

    ```bash
    $ vendor/bin/behat --tags="~@javascript"
    ```

  - Behat (JS scenarios)
 
    1. Download [Chromedriver](https://sites.google.com/a/chromium.org/chromedriver/)
    
    2. Download [Selenium Standalone Server](https://www.seleniumhq.org/download/).
    
    2. Run Selenium server with previously downloaded Chromedriver:
    
        ```bash
        $ java -Dwebdriver.chrome.driver=chromedriver -jar selenium-server-standalone.jar
        ```
        
    3. Run test application's webserver on `localhost:8080`:
    
        ```bash
        $ (cd tests/Application && bin/console server:run localhost:8080 -d public -e test)
        ```
    
    4. Run Behat:
    
        ```bash
        $ vendor/bin/behat --tags="@javascript"
        ```

### Opening Sylius with your plugin

- Using `test` environment:

    ```bash
    $ (cd tests/Application && bin/console sylius:fixtures:load -e test)
    $ (cd tests/Application && bin/console server:run -d public -e test)
    ```
    
- Using `dev` environment:

    ```bash
    $ (cd tests/Application && bin/console sylius:fixtures:load -e dev)
    $ (cd tests/Application && bin/console server:run -d public -e dev)
    ```
