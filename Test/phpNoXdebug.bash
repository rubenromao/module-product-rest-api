#!/usr/bin/env bash
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'

phpIniPath=

#Run PHP CLI without xdebug
function phpNoXdebug {
    phpIniPath="$(mktemp -t php.XXXX).ini"
    # Using awk to ensure that files ending without newlines do not lead to configuration error
    /usr/bin/env php -i | grep "\.ini" | grep -o -e '\(/[a-z0-9._-]\+\)\+\.ini' | grep -v xdebug | xargs awk 'FNR==1{print ""}1' > "$phpIniPath"
    /usr/bin/env php -n -c "$phpIniPath" "$@"

}

#Cleanup on exit
function finish {
  [[ -f "$phpIniPath" ]] && rm -f "$phpIniPath"
}
trap finish EXIT


# Now run with the full set of arguments passed into this script
echo "running phpNoXdebug $@"
phpNoXdebug $@