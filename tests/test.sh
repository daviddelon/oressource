#!/bin/bash
echo "Initialisation base de donnée"
../vendor/bin/phpunit DatabaseTest.php --testdox
echo "Test données de gestion"
../vendor/bin/phpunit GestionTest.php --testdox
echo "Test entrées / sorties d'objets "
../vendor/bin/phpunit MouvementsTest.php --testdox
