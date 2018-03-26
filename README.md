# preservation-va-site

Preservation VA - Marketing site

This is a child theme for Understrap.

# Setup

Install `wp-cli` and `mysql` as below, or using another method:

```zsh
brew install homebrew/php/wp-cli
brew install mysql
```

Now set up a database, user and download and config Wordpress into the local repo folder

```zsh
mysql -uroot -proot -e 'create database if not exists preservation_va'
mysql -uroot -proot -e "grant all privileges on preservation_va . * to preservation_va@localhost identified by 'preservation_va'"
mysql -uroot -proot -h 0.0.0.0 -e 'flush privileges'
wp core download
wp core install --url=localhost:8080 --title="Preservation VA" --admin_user="Empirical" --admin_password="SOME-PASSWORD" --admin_email="noah@goempirical.com"
wp core config --dbname=preservation_va --dbuser=preservation_va --dbpass=preservation_va --dbhost=0.0.0.0
```

Now within your local repo folder, install this repo. You can do that with a git clone.

```zsh
git clone https://github.com/goempirical/preservation-va-site.git
```

Now change to the directory wp-content/themes/understrap and run npm install

```zsh
cd wp-content/themes/understrap
npm install
```

Now change to the directory wp-content/themes/preservation-va-site and run npm install

```zsh
cd wp-content/themes/preservation-va-site
npm install
```

# Seba's Notes

Start MySQL

```
> mysqld
```

Stop

```
> mysqld Stop
```

Start WP server

```
> wp server
```
