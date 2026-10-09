# Getting Started

Welcome to the docs of **R3T**, and thank you for showing interest to it!

## Overview

**R3T** is a **R**elational Data **T**ransformation and **T**ransfer **T**ool.

You register a sequence of operations to the R3T operation manager, and execute the php script.

Then you are all done! All your data from the old DB is now correctly inserted into the new DB.

## Install

Clone this [repository](https://github.com/Raymondoor/R3T) and require in your php file
```php
<?php
require_once '/path/to/R3T.php'; // adjust accordingly
use R3T\R3T;
// ... rest of your code
```
OR

Use [Composer](https://getcomposer.org/) and run
```sh
composer require raymondoor/r3t
```
then
```php
<?php
require_once '/path/to/vendor/autoload.php'; // adjust accordingly
use R3T\R3T;
// ... rest of your code
```

that's it!

## Why R3T?

Database migrations can become difficult to maintain when the source and target have different structures or when data must be combined and transformed along the way. A collection of procedural scripts can quickly become difficult to understand as these requirements grow.

**R3T approaches migrations as compositions of operations.** Instead of writing one large script that handles everything, you define smaller operations that transform data step by step before transferring it to the target database.

This makes complex data migrations easier to organize, understand, and maintain.
