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
                sh "composer install && npm install && php bin/console cache:clear"
                sh "ssh stefke@206.189.53.20 rm -rf /var/www/controllet/*"
                sh "rsync -avzuh -e ssh . stefke@206.189.53.20:/var/www/controllet/"
            }
        }
    }

}