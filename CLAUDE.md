# Arara PHP SDK - Maintenance Guide

Official PHP SDK for the Arara WhatsApp API (https://ararahq.com).

## Project Status

This SDK is **under active development**. The core structure is complete, but the package has not yet been published or tested against the live Arara API.

### Implemented Features

- **Messages**: Send template messages via WhatsApp
- **Templates**: List and retrieve available templates
- **Webhooks**: Full CRUD operations for webhook management
- **Phone Helper**: Brazilian phone number formatting and validation
- **Error Handling**: Typed exceptions for all API error scenarios

## Architecture Overview

```
src/
├── Arara.php                    # Main client (entry point)
├── AraraClient.php              # Internal HTTP client
├── Config.php                   # SDK configuration
├── Contracts/
│   ├── HttpClientInterface.php  # HTTP client interface
│   └── ResourceInterface.php    # Base resource interface
├── DTOs/
│   ├── Message/
│   │   ├── MessageResponse.php
│   │   └── SendMessageData.php
│   ├── Template/
│   │   └── TemplateData.php
│   └── Webhook/
│       └── WebhookData.php
├── Exceptions/
│   ├── ApiException.php
│   ├── AraraException.php
│   ├── AuthenticationException.php
│   ├── RateLimitException.php
│   └── ValidationException.php
├── Http/
│   ├── GuzzleHttpClient.php
│   └── Response.php
├── Resources/
│   ├── AbstractResource.php
│   ├── Messages.php
│   ├── Templates.php
│   └── Webhooks.php
└── Support/
    └── PhoneNumber.php
```

## Code Standards

### Style Requirements

- **PSR-12** coding standard
- **Strict types** in all files (`declare(strict_types=1);`)
- **Type hints** on all parameters and return types
- **PHPStan level 8** compliance
- **Readonly properties** where applicable
- **Constructor property promotion**

### Before Committing

Always run the quality checks:

```bash
# Run all checks
composer check

# Or individually:
composer test      # PHPUnit tests
composer analyse   # PHPStan analysis
composer format    # PHP-CS-Fixer
```

## Maintenance Tasks

### Bug Fixes

1. Create a test that reproduces the bug
2. Fix the issue in the relevant class
3. Ensure all existing tests pass
4. Run PHPStan to verify type safety
5. Update CHANGELOG.md

### Updating Dependencies

```bash
composer update
composer test
composer analyse
```

### API Changes

When the Arara API changes:

1. Check the official documentation: https://docs.ararahq.com/api-reference
2. Update the relevant Resource class
3. Update or create DTOs as needed
4. Add/update tests
5. Update README examples if affected

## Adding New Features

### New Resource

1. Create the Resource class in `src/Resources/`
2. Extend `AbstractResource`
3. Create DTOs in `src/DTOs/{ResourceName}/`
4. Add the resource getter to `Arara.php`
5. Write unit tests with mocked HTTP client
6. Update README documentation

### New DTO

Follow this pattern:

```php
<?php

declare(strict_types=1);

namespace Arara\DTOs\{Category};

final readonly class NewData
{
    public function __construct(
        public string $requiredField,
        public ?string $optionalField = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            requiredField: $data['required_field'],
            optionalField: $data['optional_field'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'required_field' => $this->requiredField,
            'optional_field' => $this->optionalField,
        ];
    }
}
```

### New Exception

Only create new exceptions for distinct error scenarios that users need to handle differently.

## Testing Guidelines

### Unit Tests

- Use **Mockery** for mocking the HTTP client
- Test both success and error scenarios
- Test DTO serialization/deserialization

### Test Structure

```php
<?php

declare(strict_types=1);

namespace Arara\Tests\Unit\Resources;

use Arara\Contracts\HttpClientInterface;
use Arara\Http\Response;
use Arara\Resources\NewResource;
use Arara\Tests\TestCase;
use Mockery;
use Mockery\MockInterface;

final class NewResourceTest extends TestCase
{
    private HttpClientInterface&MockInterface $client;
    private NewResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(HttpClientInterface::class);
        $this->resource = new NewResource($this->client);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_do_something(): void
    {
        $this->client
            ->shouldReceive('post')
            ->once()
            ->andReturn(new Response(
                statusCode: 200,
                body: json_encode(['data' => ['id' => '123']]),
            ));

        $result = $this->resource->doSomething();

        $this->assertSame('123', $result->id);
    }
}
```

## Error Handling Reference

| HTTP Status | Exception                  | Use Case                    |
|-------------|----------------------------|-----------------------------|
| 401         | AuthenticationException    | Invalid or missing API Key  |
| 422         | ValidationException        | Invalid request data        |
| 429         | RateLimitException         | Rate limit exceeded         |
| 4xx         | ApiException               | Other client errors         |
| 5xx         | ApiException               | Server errors (with retry)  |

## API Reference

**Base URL**: `https://api.ararahq.com`
**Documentation**: https://docs.ararahq.com/api-reference

### Current Endpoints

```
POST /v1/messages/send     - Send template message
GET  /v1/templates         - List templates
GET  /v1/templates/{name}  - Get template by name
GET  /v1/webhooks          - List webhooks
POST /v1/webhooks          - Create webhook
GET  /v1/webhooks/{id}     - Get webhook by ID
PATCH /v1/webhooks/{id}    - Update webhook
DELETE /v1/webhooks/{id}   - Delete webhook
```

## Release Process

1. Update version in relevant files
2. Update CHANGELOG.md with changes
3. Run full test suite: `composer check`
4. Create git tag: `git tag v1.x.x`
5. Push tag: `git push origin v1.x.x`
6. Create GitHub release with changelog

## Future Roadmap

### Planned

- [ ] **arara-laravel** package - Laravel integration with ServiceProvider, Facade, config publishing
- [ ] GitHub Actions CI/CD pipeline
- [ ] Packagist publication

### Potential Enhancements

- PSR-3 Logger integration for debugging
- Webhook signature verification (if Arara implements signing)
- Media message support (when available in API)
- Conversation/session management (when available in API)

## Useful Commands

```bash
# Development
composer install          # Install dependencies
composer test             # Run PHPUnit tests
composer analyse          # Run PHPStan
composer format           # Run PHP-CS-Fixer
composer check            # Run all checks

# Git
git status                # Check changes
git diff                  # View changes
git log --oneline -10     # Recent commits
```

## Support

- **API Documentation**: https://docs.ararahq.com/api-reference
- **Issues**: Report bugs and feature requests on GitHub
- **SDK Design Reference**: stripe/stripe-php, twilio/sdk
