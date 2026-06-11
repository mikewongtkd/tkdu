# TKDU Docker Build

## Dependencies

The development environment must have `git` and `composer` installed, as well as (of course) Docker. In the `gui/frontend` directory, run `composer install` to install the PHP Composer dependencies for the project; these dependencies will be shared with the docker container when the volumes are mounted.

## Synopsis

To build the GUI frontend, link the `bin` and the `gui` directories in the registration repository to these directories and run the following commands:

    perl Makefile.PL
    make

<!-- vim: set wrap linebreak: -->
