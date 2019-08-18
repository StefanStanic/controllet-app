pipeline {
    agent any

    stages {
        stage ('Composer install phase') {
            steps {
                sh "composer install"
            }
        }

        stage ('Asset build phase') {
            steps {
                sh "npm install"
                sh "yarn run encore production"
            }
        }

        stage ('Build cleanup and deploy') {
            steps {
                sh "rm -rf node_modules vendor var node_modules"
                sh "ssh stefke@206.189.53.20 rm -rf /var/www/controllet/*"
                sh "scp -r * stefke@206.189.53.20:/var/www/controllet/"
            }
        }

        stage ('Build on production') {
            steps {
                sh "ssh stefke@206.189.53.20 cd /var/www/controllet && composer install && npm install && php bin/console cache:clear"
            }
        }

    }

}