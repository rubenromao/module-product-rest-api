#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'
echo "
===========================================
$(hostname) $0 $@
===========================================
"

testing=${1:-'no'}
pathToMagentoBin='../../bin/magento';
if [[ "${testing}" == 'testing' ]]
then
  echo "Installing in test mode"
  databaseName='rubenromao_testing'
  databaseUser='rubenromao_testing'
  databasePassword='password1'
  baseUrl='http://127.0.0.1:8082'
  rewrites=0
else
  echo "Installing in Live mode"
  databaseName='magento'
  databaseUser='root'
  databasePassword="$(< /dev/urandom tr -dc _A-Z-a-z-0-9 | head -c32;echo;)"
  if [[ `hostname` == *"desktop"* ]]
  then
    baseUrl='https://testlpw.dev/'
  else
    baseUrl='https://testlpw.dev/'
  fi
  rewrites=0
fi
mysql -e "DROP DATABASE IF EXISTS ${databaseName}"
mysql -e "CREATE DATABASE ${databaseName} CHARACTER SET utf8 COLLATE utf8_general_ci;"
mysql -e "
 GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, REFERENCES, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EVENT, TRIGGER
 ON ${databaseName}.*
 TO '${databaseUser}'@'localhost' IDENTIFIED BY '${databasePassword}';
 flush privileges;
"


php ${pathToMagentoBin} setup:install --base-url=${baseUrl} \
--db-host=localhost --db-name=${databaseName} --db-user=${databaseUser} --db-password=${databasePassword} \
--admin-firstname=Magento --admin-lastname=User --admin-email=user@example.com \
--admin-user=admin --admin-password=admin123 --language=en_US \
--currency=USD --timezone=America/Chicago --use-rewrites=${rewrites}

echo "
----------------
$(hostname) $0 completed
----------------
"



