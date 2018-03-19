# preservation-va-site

Preservation VA - Marketing site

This is a child theme for Understrap.

# Setup

Install `wp-cli` and `mysql` as below, or using another method:

```zsh
brew install homebrew/php/wp-cli
brew install mysql
```

Now set up a database, user and download and config Wordpress into the folder of your choice--DON'T DO THIS IN THE FOLDER FOR THIS REPO, do it in a blank folder.

```zsh
mysql -uroot -proot -e 'create database if not exists preservation_va'
mysql -uroot -proot -e "grant all privileges on preservation_va . * to preservation_va@localhost identified by 'preservation_va'"
mysql -uroot -proot -h 0.0.0.0 -e 'flush privileges'
wp core download
wp core install --url=localhost:8080 --title="Preservation VA" --admin_user="Empirical" --admin_password="SOME-PASSWORD" --admin_email="noah@goempirical.com"
wp core config --dbname=preservation_va --dbuser=preservation_va --dbpass=preservation_va --dbhost=0.0.0.0
```

Now change to the themes directory and install understrap.

```zsh
cd wp-content/themes
git clone https://github.com/understrap/understrap.git
```

Now within the `themes` folder, install this repo. You can do that with a git clone, or by git cloning elsewhere, the symlining if it's more convenient to have this folder elsewhere for development?

```zsh
git clone https://github.com/OKNoah/preservation-va-site.git
```
