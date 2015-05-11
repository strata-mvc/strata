#!/usr/bin/env bash

# This requires Composer to be installed on the host computer.
# Abort if the composer command is not available.
command -v composer >/dev/null 2>&1 || {
    echo ""
    echo " Composer is not installed or is at least not available through your PATH variable."
    echo " Here is an example on how to install it globally:"
    echo ""
    echo "     curl -sS https://getcomposer.org/installer | php"
    echo "     mv composer.phar /usr/local/bin/composer"
    echo ""
    echo " More info: https://getcomposer.org/doc/00-intro.md"
    echo ""

    exit;
}

# Ensure the project is ran form the correct directory before writing
# files.
cwd=$(pwd)
echo ""
echo " We determined your current path to be '${cwd}'; we will create the project directory inside. Is that correct?"
select yn in "Yes" "No"; do
    case $yn in
        Yes ) break;;
        No ) exit;;
    esac
done

# We need a project name to send to the Bedrock installer. Eventually, I guess we could
# enforce the namespace format.
echo ""
read -e -p " Please name this new project. The project name must be a valid PHP namespace (ex: MyProject) : " PROJECTNAME

if [ -z "$PROJECTNAME" ]; then
    echo " That is not a valid project name. Aborting installation.";
    exit;
fi;

# All is good, render the project.
echo ""
echo " Creating $PROJECTNAME..."
echo ""

# Require Bedrock
composer create-project roots/bedrock $PROJECTNAME
cd $PROJECTNAME

# Require Strata
composer require francoisfaubert/strata:dev-master

# Install Strata
chmod +x vendor/francoisfaubert/strata/src/Scripts/install.sh
vendor/francoisfaubert/strata/src/Scripts/install.sh
