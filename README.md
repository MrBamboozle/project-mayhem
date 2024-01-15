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

#### 4. Create storage symlink for file uploads
Important to do this before you migrate since DatabaseSeeder uses names from default avatar images to write to DB.
```
make link
```

#### 5. Make sure you migrate and seed the db
Migrate the DB
```
make migrate
```
Seed with default data
```
make seed
```

If you get `[2002] Connection refused` error, wait a minute or two for db service to properly start, and then try to migrate again.
Takes a bit of time on first run. 

Your Laravel backend should now be running. Happy coding!

#### Kill docker
```
make down
```
