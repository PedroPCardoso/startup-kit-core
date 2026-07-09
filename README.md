# Startup Kit — Core

![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) ![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white) ![License](https://img.shields.io/badge/license-MIT-blue)

Foundation package for **Startup Kit**, a modular backend for SaaS products built on **PHP 8.4 / Laravel 12** with DDD, CQRS and ports & adapters. Every other Startup Kit module depends on this one.

## What's inside

| Area | What it provides |
| --- | --- |
| **CQRS primitives** | `Command`/`Query` buses with a middleware pipeline: logging, tracing and transactional middleware |
| **Event bus + outbox** | `QueueEventBus` backed by a database transactional outbox (`DbOutbox`), so domain events are never lost mid-transaction |
| **Resilient drivers** | Redis and Database drivers behind a registry, with graceful degradation (`UnavailableDriver`) when a backend is down |
| **Contracts** | `EventBus`, `Repository`, `UnitOfWork`, `Outbox`, `Logger`, `Tracer`, `Notifier`, `ResilientDriver` |
| **Operational API** | Health, startup and shutdown endpoints for readiness/liveness probes |
| **Logging** | Multi-channel logger implementation |

## Install

```bash
composer require pedropardoso/startup-kit-core
```

> Not yet on Packagist — add the repo as a VCS repository in your `composer.json` first:
>
> ```json
> { "repositories": [{ "type": "vcs", "url": "https://github.com/PedroPCardoso/startup-kit-core" }] }
> ```

The service provider (`StartupKitCoreServiceProvider`) is auto-discovered by Laravel. Configuration lives in `config/startup-kit-core.php` and the outbox table ships as a migration.

## Testing

```bash
composer install
vendor/bin/phpunit
```

Unit and integration tests run on Orchestra Testbench. Repository adapters share a contract test case, so every persistence backend is verified against the same behaviour.

## Startup Kit modules

| Module | Description |
| --- | --- |
| [startup-kit-core](https://github.com/PedroPCardoso/startup-kit-core) | Primitives, contracts and cross-cutting infrastructure |
| [startup-kit-users](https://github.com/PedroPCardoso/startup-kit-users) | User registration & management |
| [startup-kit-payments](https://github.com/PedroPCardoso/startup-kit-payments) | Payments with multi-gateway support (Stripe, Mercado Pago) |
| [startup-kit-orders](https://github.com/PedroPCardoso/startup-kit-orders) | Orders & order lines |
| [startup-kit-subscriptions](https://github.com/PedroPCardoso/startup-kit-subscriptions) | Recurring subscriptions |
| [startup-kit-notifications](https://github.com/PedroPCardoso/startup-kit-notifications) | Multi-channel notifications (SendGrid, Twilio, Telegram) |

## License

MIT
