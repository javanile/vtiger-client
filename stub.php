#!/usr/bin/env php
<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

Phar::mapPhar();

require 'phar://' . __FILE__ . '/bin/vtc';

__HALT_COMPILER();
