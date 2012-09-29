<?php
$application = Application::getInstance();
$application->addController(new RegisterController(), "register");
$application->addController(new LoginController(), "login");
$application->addController(new CreateLightningUploadController(), "createLightningUpload");
$application->addController(new CreateLightningTextfieldController(), "createLightningTextarea");
?>
