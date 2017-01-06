@echo.
@echo off

SET cwd=%~dp0
SET comm=
SET params=%*

IF "%params%"=="db create" (
    php "%cwd%vendor\strata-mvc\strata\src\Scripts\runner.php" %params%
) ELSE (
    vendor\bin\wp eval-file --skip-themes --color "%cwd%vendor\strata-mvc\strata\src\Scripts\runner.php" %params%
)

echo.

EXIT /B %ERRORLEVEL%