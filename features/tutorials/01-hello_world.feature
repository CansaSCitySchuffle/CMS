Feature: A simple Hello World Application Tutorial
This describes how to create step by step an Hello World Application



Background:
    Given I'm in an empty Project directory
    And I create a composer file like this:
    """
    {
        "name": "test/helloworld",
        "description": "A simple Hello World Application created with Lucas MVC",
        "require": {
            "alwinmark/lucas"
        }
    }

    """
    When I create a directory: "./web"


Scenario: The most simple Hello World Application (you don't even need any Framework :-) )
    When I create a File: "web/index.php" with:
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


# Ok this is obviously a hack, but it should demonstrate how leightweight this framework is. It's just extending with your needs.
# so let's try sth. more Advanced
#
Scenario: First custom Hello World Module embedded to your Application
    When I create a directory: "./modules/HelloWorld"
    And I create a File: "modules/HelloWorld/Module.php" with:
    """
    <?php
    namespace modules\HelloWorld;

    class Module implements \lucas\Module {
        public function serve(\lucas\Request $request) {
            return "Hello World";
        }
    }

    """
    And I create a File: "web/index.php" with:
    """
    <?php
        require('../vendor/autoload.php');
        $app = new \lucas\Application();
        $app->addModule("HelloWorld", new modules\HelloWorld\Module());
    ?>
    <html>
        <head>
            <title>01-Tutorial</title>
        </head>
        <body>
            <?= $app->getModule('HelloWorld'); ?>
        </body>
    </html>

    """
    And I am on "http://localhost:7237"
    Then I should see "Hello World"
