kw_afterload
================

[![Build Status](https://app.travis-ci.com/alex-kalanis/kw_afterload.svg?branch=master)](https://app.travis-ci.com/github/alex-kalanis/kw_afterload)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-kalanis/kw_afterload/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-kalanis/kw_afterload/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/alex-kalanis/kw_afterload/v/stable.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_afterload)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/alex-kalanis/kw_afterload.svg?v1)](https://packagist.org/packages/alex-kalanis/kw_afterload)
[![License](https://poser.pugx.org/alex-kalanis/kw_afterload/license.svg?v=1)](https://packagist.org/packages/alex-kalanis/kw_afterload)
[![Code Coverage](https://scrutinizer-ci.com/g/alex-kalanis/kw_afterload/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/alex-kalanis/kw_afterload/?branch=master)

Personal loader for KWCMS. But this is no autoloader - that is separate package.
This one is for setting steps after start - as part of bootloader. So you have mamy
smaller config files instead of one huge bootstrap. And they are loaded more
dynamically. You can set order of loaded configs or disable them completely.

Installation
------------

Copy whole project into your vendor dir under author named dir and copy things from
```example/_bootstrap.php``` to your bootstrap. Nothing more is need.

Manage
-------

For simplify management there is a whole class which allows manipulation with
configs. Just say config's name and what to do with it. Just creation is done as
disabled, not enabled.

Tests
-----

Uses PhpUnit tests. Download Phpunit.phar, save it to the root, make it executable and
run.
