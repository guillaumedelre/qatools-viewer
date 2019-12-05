#!/bin/sh
set -e

usage()
{
  echo -e "
  \e[32m
   ___      _      _____           _
  / _ \    / \    |_   _|__   ___ | |___
 | | | |  / _ \     | |/ _ \ / _ \| / __|
 | |_| | / ___ \    | | (_) | (_) | \__ \\
  \__\_\/_/   \_\   |_|\___/ \___/|_|___/

  \e[0m
  \e[33m Usage :\e[0m

    pdepend ...
    phan ...
    php-cs-fixer ...
    phpcbf ...
    phpcf ...
    phpcpd ...
    phpcs ...
    phpdcd ...
    phploc ...
    phpmd ...
    phpmetrics  ...
    phpstan ...
    security-checker ...
    simple-phpunit ...
    twigcs ...
    yaml-linter ..."

  exit
}

uid=$(stat -c %u /project)
gid=$(stat -c %g /project)

if [ $uid == 0 ] && [ $gid == 0 ]; then
  if [ $# -eq 0 ]; then
    usage
  else
    exec "$@"
  fi
fi

sed -i -r "s/foo:x:\d+:\d+:/foo:x:$uid:$gid:/g" /etc/passwd
sed -i -r "s/bar:x:\d+:/bar:x:$gid:/g" /etc/group

if [ $# -eq 0 ]; then
  usage
else
  user=$(grep ":x:$uid:" /etc/passwd | cut -d: -f1)
  exec su-exec $user "$@"
fi
