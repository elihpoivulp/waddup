# Waddup

## Instructions
### Installation
1. Download/clone this repo.
```shell
git clone git@github.com:elihpoivulp/waddup.git
```

2. Change directory into the project root.
```shell
cd waddup
```

3. Install PHP dependencies.
```shell
composer install
```

### Running
#### Using PHP's Built-in Server
1. Change directory into the `Public/` folder. <br>
*Assuming you are inside the project root (`waddup/`)*
```shell
cd ./Public
```

2. Then run,
```shell
php -S localhost:8000
```

3. Open browser and type in the address bar: `http://localhost:8000`

#### Accessing from the Server Root (e.g., `htdocs/`)
1. After cloning or extracting the repo, move the project folder (`waddup/`) to your webserver's root folder (e.g., `htdocs/`).
```shell
mv ./waddup /path/to/htdocs/
```

2. Open browser and type in: `http://localhost/waddup/`