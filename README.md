## NFLS.IO Main Repository
[![License: AGPL v3](https://img.shields.io/badge/License-AGPL%20v3-blue.svg)](https://www.gnu.org/licenses/agpl-3.0)

This repository contains code for backend including api service and frontend for web service. 
We are now using Symfony as our backend framework, and Vue as our frontend framework.
The code is currently used for https://nfls.io.

## Installation
### Environment
PHP >= 7.1, MySql >= 5.6, Redis, Composer, NPM, Bower
### Configuration
Copy and configure all parameters form .env.dist correctly in .env
### Development
1. Install dependencies from composer and npm in the root directory.
2. Install bower dependencies in public/assets directory. 
3. Migrate the database using bin/console doctrine:schema:update --force.
4. (For Linux/maxOS)Run dev.sh. 
5. (For Windows)Run ```bin/console server:run``` and ```node_modules/.bin/encore dev --watch```
 in two separated consoles.
6. Now the website is available locally.
### Production
1. Run prod.sh, it will automatically install all the dependencies and migrate the database.

## Thank
### BrowserStack
<a href="https://browserstack.com"><img src="https://bstacksupport.zendesk.com/attachments/token/Ygvb0OdLftxe7bMxq5JHzEhQh/?name=browserstack-logo-600x315.png" width="200"/></a>

We are using BrowserStack to test frontend compatibity on all major devices.

## Contribution
For all the things related to development(e.g. issues, releasing schedules), please visit https://dev.nfls.io.
