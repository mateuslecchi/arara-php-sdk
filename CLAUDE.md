# Arara PHP SDK

SDK PHP para integração com a API de WhatsApp da Arara (https://ararahq.com).

## Visão Geral do Projeto

Este é um pacote PHP **puro** (framework-agnostic) que serve como SDK para a API da Arara, uma plataforma brasileira de comunicação via WhatsApp. O pacote deve funcionar em qualquer projeto PHP 8.2+ sem dependências de frameworks específicos.

Posteriormente, será criado um pacote separado `arara-laravel` que usará este como dependência e adicionará integrações específicas do Laravel (ServiceProvider, Facade, config publishing, etc.).

## Documentação da API

**URL Base:** `https://api.ararahq.com` (verificar na documentação oficial)
**Documentação:** https://docs.ararahq.com/api-reference

### Autenticação

- Tipo: Bearer Token (API Key)
- Header: `Authorization: Bearer {API_KEY}`
- A API Key é obtida no dashboard da Arara e mostrada apenas uma vez

### Endpoints Conhecidos (verificar documentação para lista completa)

```
POST /v1/messages/send     - Enviar mensagem de template
GET  /v1/templates         - Listar templates
POST /v1/webhooks          - Configurar webhooks
```

### Funcionalidades Principais

1. **Envio de Mensagens de Template** - Enviar mensagens via templates pré-aprovados
2. **Gerenciamento de Templates** - Listar e consultar templates disponíveis
3. **Webhooks** - Configurar e receber notificações de eventos (ex: carrinho de compras)

## Arquitetura do Pacote

```
arara-php/
├── src/
│   ├── Arara.php                      # Cliente principal (entry point)
│   ├── AraraClient.php                # Cliente HTTP interno
│   ├── Config.php                     # Configurações do SDK
│   │
│   ├── Contracts/
│   │   ├── HttpClientInterface.php    # Interface para cliente HTTP
│   │   └── ResourceInterface.php      # Interface base para resources
│   │
│   ├── Http/
│   │   ├── GuzzleHttpClient.php       # Implementação com Guzzle
│   │   └── Response.php               # Wrapper de resposta
│   │
│   ├── Resources/
│   │   ├── AbstractResource.php       # Classe base para resources
│   │   ├── Messages.php               # Resource de mensagens
│   │   ├── Templates.php              # Resource de templates
│   │   └── Webhooks.php               # Resource de webhooks
│   │
│   ├── DTOs/
│   │   ├── Message/
│   │   │   ├── SendMessageData.php    # DTO para envio de mensagem
│   │   │   └── MessageResponse.php    # DTO de resposta
│   │   ├── Template/
│   │   │   └── TemplateData.php       # DTO de template
│   │   └── Webhook/
│   │       └── WebhookData.php        # DTO de webhook
│   │
│   ├── Exceptions/
│   │   ├── AraraException.php         # Exception base
│   │   ├── AuthenticationException.php
│   │   ├── ValidationException.php
│   │   ├── RateLimitException.php
│   │   └── ApiException.php
│   │
│   └── Support/
│       ├── PhoneNumber.php            # Helper para formatação de telefone BR
│       └── Arr.php                    # Helper de arrays (se necessário)
│
├── tests/
│   ├── Unit/
│   │   ├── AraraTest.php
│   │   ├── Resources/
│   │   │   ├── MessagesTest.php
│   │   │   ├── TemplatesTest.php
│   │   │   └── WebhooksTest.php
│   │   └── DTOs/
│   │       └── SendMessageDataTest.php
│   │
│   └── Feature/
│       └── Integration/
│           └── SendMessageTest.php    # Testes com API real (opcional)
│
├── composer.json
├── phpunit.xml
├── phpstan.neon
├── .php-cs-fixer.php
├── README.md
├── CHANGELOG.md
├── LICENSE
└── .github/
    └── workflows/
        ├── tests.yml
        └── static-analysis.yml
```

## Especificações Técnicas

### Requisitos

- PHP 8.2+
- Guzzle HTTP 7.0+
- ext-json

### composer.json

```json
{
    "name": "seuvendor/arara-php",
    "description": "SDK PHP para a API de WhatsApp da Arara",
    "keywords": ["whatsapp", "arara", "sdk", "api", "sms", "messaging", "brazil"],
    "license": "MIT",
    "type": "library",
    "require": {
        "php": "^8.2",
        "guzzlehttp/guzzle": "^7.0",
        "ext-json": "*"
    },
    "require-dev": {
        "phpunit/phpunit": "^11.0",
        "phpstan/phpstan": "^1.10",
        "friendsofphp/php-cs-fixer": "^3.0",
        "mockery/mockery": "^1.6"
    },
    "autoload": {
        "psr-4": {
            "Arara\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Arara\\Tests\\": "tests/"
        }
    },
    "config": {
        "sort-packages": true
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

## Exemplos de Uso Final

### Inicialização

```php
use Arara\Arara;

// Forma simples
$arara = new Arara('sua-api-key');

// Com configurações customizadas
$arara = new Arara('sua-api-key', [
    'base_url' => 'https://api.ararahq.com',
    'timeout' => 30,
    'retry' => [
        'times' => 3,
        'delay' => 100,
    ],
]);
```

### Enviar Mensagem de Template

```php
use Arara\DTOs\Message\SendMessageData;

// Forma fluente
$response = $arara->messages()->send(
    to: '5511999999999',
    templateName: 'order_confirmation',
    parameters: [
        'nome' => 'João Silva',
        'pedido' => '12345',
        'valor' => 'R$ 150,00'
    ],
    language: 'pt_BR'
);

// Usando DTO
$message = new SendMessageData(
    to: '5511999999999',
    templateName: 'order_confirmation',
    parameters: ['nome' => 'João']
);

$response = $arara->messages()->send($message);

// Verificar resultado
if ($response->isSuccessful()) {
    echo "Mensagem enviada! ID: " . $response->messageId();
}
```

### Listar Templates

```php
$templates = $arara->templates()->list();

foreach ($templates as $template) {
    echo $template->name . ' - ' . $template->status;
}
```

### Configurar Webhook

```php
$arara->webhooks()->create([
    'url' => 'https://meusite.com/webhook/arara',
    'events' => ['message.delivered', 'message.read', 'cart.updated'],
]);
```

## Fases de Desenvolvimento

### Fase 1: Estrutura Base ✅
- [ ] Inicializar projeto com composer
- [ ] Criar estrutura de diretórios
- [ ] Configurar autoload PSR-4
- [ ] Criar classe Config
- [ ] Criar interfaces (Contracts)
- [ ] Criar exceptions

### Fase 2: Cliente HTTP
- [ ] Implementar HttpClientInterface
- [ ] Implementar GuzzleHttpClient
- [ ] Implementar Response wrapper
- [ ] Adicionar retry logic
- [ ] Adicionar timeout handling
- [ ] Tratar erros HTTP (4xx, 5xx)

### Fase 3: Cliente Principal
- [ ] Implementar classe Arara (entry point)
- [ ] Implementar AraraClient interno
- [ ] Configurar autenticação Bearer
- [ ] Adicionar headers padrão

### Fase 4: Resources
- [ ] Implementar AbstractResource
- [ ] Implementar Messages resource
- [ ] Implementar Templates resource
- [ ] Implementar Webhooks resource

### Fase 5: DTOs
- [ ] Criar SendMessageData
- [ ] Criar MessageResponse
- [ ] Criar TemplateData
- [ ] Criar WebhookData

### Fase 6: Helpers
- [ ] Implementar PhoneNumber (formatação BR)
- [ ] Validação de números brasileiros

### Fase 7: Testes
- [ ] Configurar PHPUnit
- [ ] Testes unitários para cada classe
- [ ] Mocks do Guzzle para testes HTTP
- [ ] Testes de integração (opcional)

### Fase 8: Qualidade
- [ ] Configurar PHPStan (level 8)
- [ ] Configurar PHP-CS-Fixer
- [ ] Configurar GitHub Actions
- [ ] Adicionar badges ao README

### Fase 9: Documentação
- [ ] README completo com exemplos
- [ ] CHANGELOG
- [ ] Documentar todas as classes (PHPDoc)

### Fase 10: Publicação
- [ ] Criar repositório GitHub
- [ ] Registrar no Packagist
- [ ] Criar release v1.0.0

## Padrões de Código

### Estilo

- PSR-12
- Strict types em todos os arquivos
- Type hints em todos os parâmetros e retornos
- Properties promotion no construtor
- Named arguments quando melhorar legibilidade
- Readonly properties quando aplicável

### Exemplo de Classe

```php
<?php

declare(strict_types=1);

namespace Arara\Resources;

use Arara\Contracts\HttpClientInterface;
use Arara\DTOs\Message\SendMessageData;
use Arara\DTOs\Message\MessageResponse;
use Arara\Exceptions\ApiException;

final class Messages extends AbstractResource
{
    public function send(SendMessageData|array $data): MessageResponse
    {
        if (is_array($data)) {
            $data = SendMessageData::fromArray($data);
        }

        $response = $this->client->post('/v1/messages/send', $data->toArray());

        if (!$response->isSuccessful()) {
            throw ApiException::fromResponse($response);
        }

        return MessageResponse::fromArray($response->json());
    }
}
```

### Exemplo de DTO

```php
<?php

declare(strict_types=1);

namespace Arara\DTOs\Message;

final readonly class SendMessageData
{
    public function __construct(
        public string $to,
        public string $templateName,
        public array $parameters = [],
        public string $language = 'pt_BR',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            to: $data['to'],
            templateName: $data['template_name'] ?? $data['templateName'],
            parameters: $data['parameters'] ?? [],
            language: $data['language'] ?? 'pt_BR',
        );
    }

    public function toArray(): array
    {
        return [
            'to' => $this->to,
            'template_name' => $this->templateName,
            'parameters' => $this->parameters,
            'language' => $this->language,
        ];
    }
}
```

### Exemplo de Exception

```php
<?php

declare(strict_types=1);

namespace Arara\Exceptions;

use Arara\Http\Response;

class ApiException extends AraraException
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly ?array $errors = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            message: $body['message'] ?? 'Erro na API',
            statusCode: $response->status(),
            errors: $body['errors'] ?? null,
        );
    }
}
```

## Tratamento de Erros

### HTTP Status Codes

| Status | Exception | Descrição |
|--------|-----------|-----------|
| 401 | AuthenticationException | API Key inválida ou ausente |
| 422 | ValidationException | Dados inválidos |
| 429 | RateLimitException | Rate limit excedido |
| 4xx | ApiException | Erro do cliente |
| 5xx | ApiException | Erro do servidor |

### Exemplo de Tratamento

```php
use Arara\Exceptions\AuthenticationException;
use Arara\Exceptions\ValidationException;
use Arara\Exceptions\RateLimitException;
use Arara\Exceptions\ApiException;

try {
    $arara->messages()->send(...);
} catch (AuthenticationException $e) {
    // API Key inválida
} catch (ValidationException $e) {
    // Dados inválidos - $e->errors contém detalhes
} catch (RateLimitException $e) {
    // Aguardar antes de tentar novamente
    sleep($e->retryAfter);
} catch (ApiException $e) {
    // Outro erro da API
}
```

## Helpers para Brasil

### PhoneNumber

```php
use Arara\Support\PhoneNumber;

// Formatar para padrão E.164
PhoneNumber::format('11999999999');      // +5511999999999
PhoneNumber::format('(11) 99999-9999');  // +5511999999999
PhoneNumber::format('+5511999999999');   // +5511999999999

// Validar número brasileiro
PhoneNumber::isValid('11999999999');     // true
PhoneNumber::isValid('1199999999');      // false (faltando dígito)

// Detectar tipo
PhoneNumber::isMobile('11999999999');    // true
PhoneNumber::isLandline('1133334444');   // true
```

## Notas de Implementação

### IMPORTANTE: Consultar Documentação

Antes de implementar cada resource, **SEMPRE** consultar a documentação oficial em:
- https://docs.ararahq.com/api-reference/authentication
- https://docs.ararahq.com/api-reference/endpoint/send
- https://docs.ararahq.com/api-reference/endpoint/webhook

Os endpoints, parâmetros e respostas documentados aqui são baseados na introdução da documentação. A implementação deve seguir a especificação oficial da API.

### Pontos de Atenção

1. **Rate Limiting**: Verificar se a API tem rate limiting e implementar backoff
2. **Retry Logic**: Implementar retry automático para erros 5xx e timeouts
3. **Validação**: Validar dados localmente antes de enviar para a API
4. **Logging**: Considerar PSR-3 LoggerInterface opcional para debug
5. **Webhook Verification**: Se a Arara assinar webhooks, implementar verificação

### Referências de SDKs Similares

Consultar para inspiração de design:
- `stripe/stripe-php` - Excelente exemplo de SDK PHP
- `twilio/sdk` - Bom exemplo de API de messaging
- `laravel/vonage-notification-channel` - Integração com Vonage

## Comandos Úteis

```bash
# Instalar dependências
composer install

# Rodar testes
composer test
# ou
./vendor/bin/phpunit

# Análise estática
composer analyse
# ou
./vendor/bin/phpstan analyse

# Formatar código
composer format
# ou
./vendor/bin/php-cs-fixer fix

# Todos os checks
composer check
```

## Scripts do composer.json

```json
{
    "scripts": {
        "test": "phpunit",
        "analyse": "phpstan analyse src tests --level=8",
        "format": "php-cs-fixer fix",
        "check": [
            "@analyse",
            "@test"
        ]
    }
}
```
