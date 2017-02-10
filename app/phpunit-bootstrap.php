<?php

// Ref https://github.com/symfony/symfony/issues/21534#issuecomment-278278352
// Polyfill PHPUnit 6.0 both ways
if (!class_exists('\PHPUnit\Framework\TestCase', true)) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
} elseif (!class_exists('\PHPUnit_Framework_TestCase', true)) {
    class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}

require __DIR__.'/autoload.php';
