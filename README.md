## Setup

There's no database for this project, so the process to get everything up and running is pretty quick:

1. `composer install` to get the back-end dependencies
2. `npm install` to get the front-end dependencies
3. `sail up -d` to spin up the API in a container
4. `npm run dev` to start the front-end It should be accessible at `http://localhost`, but the console will tell you where.

If you have any issues running Laravel Sail, see their docs: https://laravel.com/docs/11.x/sail#installing-sail-into-existing-applications

## Back-end

A number of tests have been provided, these can be run inside the container by calling `sail artisan test`.

Test coverage:
Initially I wrote some unit tests to cover leap year handling, but it was much more sensible to utilise existing date handling libraries. I have left the tests in place for reference.

The feature tests cover handling the request validation, as well as various scenarios to account for date-shifting of the last-day-but-one requirement for the transfer to be completed.

Currently only on the back-end, but I reconfigured the controller to take a property of `backdate_transfer_date`, which apply the same backdading logic to the transfer date as it on the payday date.

## Front-end

This was a pretty basic implementation of the requirements, using the Vue starter files that Laravel allows when initialising an application. I simply reused some existing components, applied some custom styling with Tailwind, and made it _vaguely_ component-y.

The front-end consists of two numeric fields, one for the month, one for the year. Submitting the form will reveal the calculated properties for:
1. The day that the transfer to the employees should be initiated
2. The latest possible date the money should arrive in the employees' accounts (based on the criteria in the task)

As discussed previously in this README, the front-end does not utilise the toggle to _also_ backdate the transfer date if it lands on a weekend or Bank Holiday, but implementing this feature would be trivial.
