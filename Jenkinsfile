pipeline {
    agent any

    stages {
        stage ('Composer install phase') {
            steps {
                sh "composer install"
            }
        }

        stage ('SSH and move project to live') {
            steps {
                sh "ls -lah"
            }
        }
    }

}