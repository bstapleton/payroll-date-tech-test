## Setup

There's no database for this project, so the process to get everything up and running is pretty quick:

1. `composer install` to get the back-end dependencies
2. `npm install` to get the front-end dependencies
3. `sail up -d` to spin up the API in a container
4. `npm run dev` to start the front-end It should be accessible at `http://localhost`, but the console will tell you where.

## Back-end

A number of tests have been provided, these can be run inside the container by calling `sail artisan test`.

Test coverage:
Initially I wrote some unit tests to cover leap year handling, but it was much more sensible to utilise existing date handling libraries. I have left the tests in place for reference.

The feature tests cover handling the request validation, as well as various scenarios to account for date-shifting of the last-day-but-one requirement for the transfer to be completed.

I wasn't sure if the same date shifting rule should apply to the transfer date (4 days prior) as well, so at time of writing it doesn't do that, but I might revisit it once I have completed the front-end parts of the task.

Discussion about implementation:
TODO

## Front-end

TODO
