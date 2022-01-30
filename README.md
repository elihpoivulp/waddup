# Waddup
Requirements
- PHP (8.1)
- Composer
- MySQL
- NPM

## Installation
1. Download/clone this repo.
```shell
git clone git@github.com:elihpoivulp/waddup.git
```

2. After cloning or extracting the repo, move the project folder (`waddup/`) to your webserver's root folder (e.g., `htdocs/`).
```shell
mv ./waddup /path/to/htdocs/
```

3. Change directory into the project root.
```shell
cd /path/to/htdocs/waddup
```

4. Install PHP and frontend dependencies.
```shell
composer install
npm install
npm run build:jquery
```

5. Create a copy of `.env-example` file and rename it `.env`
```shell
cp ./.env-example ./.env
```

6. Edit `.env` file with your database and email (optional-however email for forgot password feature will not work) configurations.


7. Upload the database script file to your database.


9. Run in the browser.
```text
http://localhost/waddup
```

10. Register for an account at
```text
http://localhost/waddup/register
```

*Note: if the DEBUG is off in the .env, this will create a folder Logs/ in the project root level. Make sure that you have appropriate permissions for this to work*