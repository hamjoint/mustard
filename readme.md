## Mustard

Mustard is an open source marketplace platform, similar to [eBay](http://www.ebay.com/). It is developed and maintained by [Hamjoint CIC](http://www.hamjoint.org/) for [Hamjoint Market](https://www.hamjoint.com/market), a marketplace dedicated to amateur radio and electronics.

Mustard is dependent on several components of the [Laravel framework](http://laravel.com), and supports [Composer](https://getcomposer.org/) for dependency management. Building a Mustard-based application requires some knowledge of PHP development.

### Features

* User accounts
* Classifieds adverts
* Email notifications
* Auctions (via [mustard-auctions](https://github.com/hamjoint/mustard-auctions) module)
* Purchasing system (via [mustard-commerce](https://github.com/hamjoint/mustard-commerce) module)
* User feedback (via [mustard-feedback](https://github.com/hamjoint/mustard-feedback) module)
* Paypal support (via [mustard-paypal](https://github.com/hamjoint/mustard-paypal) module)
* Stripe support (via [mustard-stripe](https://github.com/hamjoint/mustard-stripe) module)
* Messaging (via [mustard-messaging](https://github.com/hamjoint/mustard-messaging) module)
* Photo & video uploads (via [mustard-media](https://github.com/hamjoint/mustard-media) module)
* Full documentation

### Requirements

See composer.json.

### Installation

#### Via Composer (using Packagist)

```sh
composer require hamjoint/mustard
```

### Licence

Mustard is free and gratis software licensed under the [GPL3 licence](https://www.gnu.org/licenses/gpl-3.0). This allows you to use Mustard for commercial purposes, but any derivative works (adaptations to the code) must also be released under the same licence. Mustard is built upon the [Laravel framework](http://laravel.com), which is licensed under the [MIT licence](http://opensource.org/licenses/MIT).
