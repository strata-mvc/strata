module.exports = function (grunt) {

    // Project config
    grunt.initConfig({

        // read grunt tasks from npm
        pkg: grunt.file.readJSON('package.json'),

        phpunit: {
            classes: {
                dir: 'tests/'
            },
            options: {
                bin: 'vendor/bin/phpunit',
                bootstrap: 'tests/bootstrap.php',
                colors: true
            }
        },

        watch: {
          scripts: {
            files: ['**/*.php', '**/**/*.php'],
            tasks: ['phpunit'],
            options: {
                spawn: false,
                debounceDelay: 20,
            },
          },
        },
    });

    grunt.loadNpmTasks('grunt-phpunit');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['phpunit']);
};
