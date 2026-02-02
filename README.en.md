# Arara PHP SDK

Official PHP SDK for integration with the [Arara](https://ararahq.com) WhatsApp API.

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

> **[Leia em Português](README.md)**

## About

This SDK allows you to easily integrate your PHP application with the Arara API for sending WhatsApp messages, managing templates, and configuring webhooks.

## Requirements

- PHP 8.2 or higher
- JSON extension
- Composer

## Installation

```bash
composer require arara/php-sdk
```

## Getting Started

### Initialization

```php
use Arara\Arara;

// Simple form
$arara = new Arara('your-api-key');

// With custom settings
$arara = new Arara('your-api-key', [
    'base_url' => 'https://api.ararahq.com',
    'timeout' => 30,
    'retry' => [
        'times' => 3,
        'delay' => 100,
    ],
]);
```

### Send Template Message

```php
use Arara\DTOs\Message\SendMessageData;

// Using array
$response = $arara->messages()->send([
    'to' => '5511999999999',
    'template_name' => 'order_confirmation',
    'parameters' => [
        'name' => 'John Doe',
        'order' => '12345',
        'amount' => '$150.00',
    ],
    'language' => 'en_US',
]);

// Using DTO
$message = new SendMessageData(
    to: '5511999999999',
    templateName: 'order_confirmation',
    parameters: ['name' => 'John'],
);

$response = $arara->messages()->send($message);

// Check result
if ($response->isSuccessful()) {
    echo "Message sent! ID: " . $response->messageId();
}
```

### List Templates

```php
$templates = $arara->templates()->list();

foreach ($templates as $template) {
    echo $template->name . ' - ' . $template->status . PHP_EOL;
}

// Get specific template
$template = $arara->templates()->get('order_confirmation');

if ($template->isApproved()) {
    echo "Template approved!";
}
```

### Manage Webhooks

```php
// Create webhook
$webhook = $arara->webhooks()->create([
    'url' => 'https://mysite.com/webhook/arara',
    'events' => ['message.delivered', 'message.read', 'cart.updated'],
]);

echo "Webhook created with ID: " . $webhook->id;

// List webhooks
$webhooks = $arara->webhooks()->list();

// Update webhook
$arara->webhooks()->update($webhook->id, [
    'events' => ['message.delivered'],
]);

// Delete webhook
$arara->webhooks()->delete($webhook->id);
```

### Brazilian Phone Number Helper

```php
use Arara\Support\PhoneNumber;

// Format to E.164 standard
PhoneNumber::format('11999999999');      // +5511999999999
PhoneNumber::format('(11) 99999-9999');  // +5511999999999

// Validate Brazilian number
PhoneNumber::isValid('11999999999');     // true
PhoneNumber::isValid('1199999999');      // false (missing digit)

// Detect type
PhoneNumber::isMobile('11999999999');    // true
PhoneNumber::isLandline('1133334444');   // true

// Format for display
PhoneNumber::formatForDisplay('5511999999999'); // (11) 99999-9999
```

## Error Handling

```php
use Arara\Exceptions\AuthenticationException;
use Arara\Exceptions\ValidationException;
use Arara\Exceptions\RateLimitException;
use Arara\Exceptions\ApiException;

try {
    $arara->messages()->send([
        'to' => '5511999999999',
        'template_name' => 'order_confirmation',
    ]);
} catch (AuthenticationException $e) {
    // Invalid or missing API Key (401)
    echo "Authentication error: " . $e->getMessage();
} catch (ValidationException $e) {
    // Invalid data (422)
    echo "Validation error: " . $e->getMessage();
    print_r($e->errors); // Error details
} catch (RateLimitException $e) {
    // Rate limit exceeded (429)
    echo "Rate limit exceeded. Wait " . $e->retryAfter . " seconds.";
} catch (ApiException $e) {
    // Other API errors
    echo "Error: " . $e->getMessage() . " (Status: " . $e->statusCode . ")";
}
```

## Project Structure

```
src/
├── Arara.php                    # Main client (entry point)
├── AraraClient.php              # Internal HTTP client
├── Config.php                   # SDK configuration
├── Contracts/                   # Interfaces
├── DTOs/                        # Data Transfer Objects
│   ├── Message/
│   ├── Template/
│   └── Webhook/
├── Exceptions/                  # Custom exceptions
├── Http/                        # HTTP client (Guzzle)
├── Resources/                   # API resources
│   ├── Messages.php
│   ├── Templates.php
│   └── Webhooks.php
└── Support/                     # Helpers
    └── PhoneNumber.php
```

## Development

This project was developed with the assistance of [Claude Code](https://claude.ai/code), an AI tool by Anthropic. All code has been reviewed and validated by the maintainer.

### Install dependencies

```bash
composer install
```

### Run tests

```bash
composer test
# or
./vendor/bin/phpunit
```

### Static analysis

```bash
composer analyse
# or
./vendor/bin/phpstan analyse src tests --level=8
```

### Format code

```bash
composer format
# or
./vendor/bin/php-cs-fixer fix
```

### All checks

```bash
composer check
```

## Contributing

1. **Fork** this repository
2. Create a **Branch** for your feature (`git checkout -b feat/new-feature`)
3. Make your changes and **Commits**
4. **Push** to your fork (`git push origin feat/new-feature`)
5. Open a **Pull Request**

### Code Standards

- PSR-12
- Strict types in all files
- Type hints on all parameters and returns
- PHPStan level 8

## API Documentation

For more information about the Arara API, see the [official documentation](https://docs.ararahq.com/api-reference).

## License

This project is licensed under the [MIT License](LICENSE).
