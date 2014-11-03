Prezent/doctrine-translatable
=============================

Translatable behaviour extension for Doctrine2

[![Build Status](https://travis-ci.org/Prezent/doctrine-translatable.png?branch=master)](https://travis-ci.org/Prezent/doctrine-translatable)

Overview
--------

The Doctrine Translatable extension allows you to translate your entities into various languages
on the fly. For every entity that you want to translate, you need to implement a translation entity that holds
all the translatable fields. The original entity and the translations will have a one-to-many relationship.
The original entity must implement the `TranslatableInterface` and the translation classes must
implement the `TranslationInterface`.

This extension provides several new mappings that make working with translations easier. It also provides some
abstract base classes and traits that you can use, but these are entirely optional. You can provide your
own implementation of the interfaces.

Index
-----

1. [Installation](installation.md)
2. [Getting started](getting-started.md)
3. [Usage](usage.md)
4. [Complete examples](examples.md)
5. [Yaml mapping](yaml-mapping.md)
6. [Xml mapping](xml-mapping.md)