kw_afterload
================

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
