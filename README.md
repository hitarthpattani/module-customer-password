# Set a customer password

Since Magento 2 doesn't provide facilities to set a customers' password using backend options and cli commands. This module can come in handy during development when working with testing customer accounts and used for B2B requirements.

## Installation

```bash
composer config repositories.hitarthpattani-git vcs https://github.com/hitarthpattani/module-customer-password.git
composer require hitarthpattani/module-customer-password:dev-master
bin/magento setup:upgrade
```

## Usage 

Call the command and pass the customers email address and the new password.

```bash
php bin/magento customer:password:change test@example.com password123
```

If customer accounts are not shared between websites, a website code has to be specified with the `--website` or `-w` option.


```bash
php bin/magento customer:password:change  test@example.com password123 --website=base
```