<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext
{

    private $workingDirectory = "";
    private $cleanUpCallbacks = [];
    protected $mockPath = "";

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->workingDirectory = realpath(dirname(__FILE__) .  "/../") .  '/testapp/';
        $this->mockPath = realpath(dirname(__FILE__) .  "/../mocks/");
    }

    private function generateRandomString() {
        $length = 10;
        return  substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    private function deleteTestdir() {
        $testfolder = "{$this->workingDirectory}";
//        system("rm -rf $testfolder");
    }

    /**
     * @AfterScenario
     */
    public function cleanup() {

        $this->deleteTestdir();

        foreach($this->cleanUpCallbacks as $cb) {
            $cb();
        }
    }

    /**
     * @Given I'm in an empty Project directory
     */
    public function iMInAnEmptyProjectDirectory()
    {
        $this->deleteTestdir();
    }


    /**
     * @When I create a composer file like this:
     */
    public function iCreateAComposerFileLikeThis(PyStringNode $content)
    {
        $zipPath = "{$this->mockPath}/vendor.zip";
        $vendorMockDir = "{$this->workingDirectory}";
        $zip = new ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($vendorMockDir);
            $zip->close();
        } else {
            throw new \Exception('File to mock the Vendors folder not found.' . $zipPath);
        }

    }

    /**
     * @When I create a directory: :directoryPath
     */
    public function iCreateADirectory($directoryPath)
    {
        $realDirPath = $this->workingDirectory;
        if ($directoryPath[0] == '.') {
            $realDirPath = "{$this->workingDirectory}$directoryPath";
        } else {
            throw new \Exception('Only relative directories to the working Dir are possible');
        }

        mkdir($realDirPath, 0777, true);
    }

    /**
     * @When I create a File: :filename with:
     */
    public function iCreateAFileWith($filename, PyStringNode $content)
    {
        $realFilename = "{$this->workingDirectory}/$filename";
        if (file_put_contents($realFilename, $content) == false) {
            throw new \Exception("File: $realFilename could not be created.");
        }
    }


    private function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if($pos !== false)
        {
            $subject = substr_replace($subject, $replace, $pos,
                    strlen($search));
        }

        return $subject;
    }
}
