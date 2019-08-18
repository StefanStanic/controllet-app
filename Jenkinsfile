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

        stage ('Deployment to production ') {
            steps {
                sh "ssh stefke@206.189.53.20 rm -rf /var/www/controllet/"
                sh "ssh stefke@206.189.53.20 mkdir -p /var/www/controllet"
                sh "scp -r . stefke@206.189.53.20:/var/www/controllet/"
            }
        }
    }

}