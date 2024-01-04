# Project Mayhem

## Running the project

### Clone the repo 
```
git clone git@github.com:MrBamboozle/project-mayhem.git
```

### Frontend

#### Install dependencies

### Backend

There is a Makefile which "aliases" all commands. 

To see actual commands that are running reference the [Makefile](back/Makefile).

#### 1. Build docker images
```
make build
```

#### 2. Create .env
```
make env
```

#### 3. Start docker
```
make up
```

You can now visit http://localhost:8000. 

If there are any issues you can run:
```
make composer_i
```
This will `composer install` in the `php` service to install all packages.

#### 4. Make sure you migrate the db
```
make migrate
```
If you get `[2002] Connection refused` error, wait a minute or two for db service to properly start, and then try to migrate again.
Takes a bit of time on first run. 

Your Laravel backend should now be running. Happy coding!

#### Kill docker
```
make down
```
