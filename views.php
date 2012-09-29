<?php
$application = Application::getInstance();
$application->addViewWithKey(new IndexView(), "");
$application->addView(new RegisterView());
$application->addView(new LoginView());
$application->addView(new CreateLightningView());
$application->addView(new LightningView());
$application->addView(new LightningOverview());
$application->addView(new LogoutView());
$application->addView(new AdminView());
?>
