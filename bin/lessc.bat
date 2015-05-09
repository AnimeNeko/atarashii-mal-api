@ECHO OFF
SET BIN_TARGET=%~dp0/../vendor/oyejorge/less.php/bin/lessc
php "%BIN_TARGET%" %*
