## Setup

There's no database for this project, so the process to get everything up and running is pretty quick:

1. `composer install` to get the back-end dependencies
2. `npm install` to get the front-end dependencies
3. `sail up -d` to spin up the API in a container
4. `npm run dev` to start the front-end It should be accessible at `http://localhost`, but the console will tell you where.

## Back-end

A number of tests have been provided, these can be run inside the container by calling `sail artisan test`.

Test coverage:
TODO

Discussion about implementation:
TODO

## Front-end

TODO
