#!/bin/bash
# REMOTE_ADDRESS="git@git.wpengine.com:production/devpres.git"

export REPO_DIR=$(pwd)
export ORIGINAL_BRANCH=$(git rev-parse --abbrev-ref HEAD)
export ORIGINAL_COMMIT=$(git rev-parse HEAD)
export TEMP_BRANCH=temp-$(date +%s)
REMOTE="wp-engine"

read -n1 -p "Deploy your latest commit from $ORIGINAL_BRANCH branch to your git remote named $REMOTE? Y/N " RESPONSE

case $RESPONSE in  
  y|Y) echo OK ;; 
  n|N) exit 0 ;;
  *) exit 0 ;; 
esac

git checkout -b $TEMP_BRANCH
shopt -s extglob
rm !(".git")
rm -rf !(".git")
wp core download
# wp config create --dbname=wp_devpres --dbuser=devpres --dbpass=PFGNtsdRyrDvMEah9L5w --dbhost=127.0.0.1 --skip-check --force
mkdir wp-content/themes/understrap
mkdir wp-content/themes/preservation-va-site
git archive $ORIGINAL_COMMIT | tar -x -C wp-content/themes/preservation-va-site
wget -q -O - "$@" https://github.com/understrap/understrap/tarball/master | tar -x -C wp-content/themes/understrap
mv ./wp-content/themes/understrap/$(ls ./wp-content/themes/understrap/)/* ./wp-content/themes/understrap/
cd wp-content/themes/preservation-va-site
npm install
cd ../../../
git add --force wp-content/themes/preservation-va-site/node_modules
git add . -A
git commit -m "Deploying"
git push -f $REMOTE $TEMP_BRANCH:master
git checkout $ORIGINAL_BRANCH
git branch -D $TEMP_BRANCH
exit 0