<?php
    $app = \Strata\Strata::bootstrap(include("vendor/autoload.php"));
    $app->setDefaultNamespace();
    $app->addProjectNamespaces();
    $app->includeWordpressFixture();
    $app->includeGettextFixture();
    $app->setConfig("namespace", "Test\\Fixture");
    $app->run();
