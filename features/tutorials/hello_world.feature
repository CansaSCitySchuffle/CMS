Feature: A simple Hello World Application Tutorial
This describes how to create step by step an Hello World Application

Scenario: The most simple Hello World Application using only ViewFrames
Given I'm in an empty Project directory
When I create a composer file like this:
"""
{
    "name": "test/helloworld",
    "description": "A simple Hello World Application created with Lucas MVC",
    "require": {
        "alwinmark/lucas"
    }
}

"""
And I create a directory: "./web"
And I create a File: "web/index.php" with:
"""
<?php
    ini_set('display_errors', 'On');
    ini_set('html_errors', 0);
    error_reporting(E_ALL);

    require('../vendor/autoload.php');

    $app = new \lucas\Application();
    echo 'Hello World!';
?>

"""
And I create a directory: "./layouts"
And I create a File: "layouts/default.php" with:
"""
<html>
    <head>
        <title>Hello World</title>
    </head>
    <body>
        Hello World!
    </body>
</html>

"""
And I am on "http://localhost:7237"
Then I should see "Hello World!"

