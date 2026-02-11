# Arara PHP SDK

SDK PHP para integração com a API da [Arara](https://ararahq.com).

[![PHP Version](https://img.shields.io/badge/php-%5E8.4-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

> **[Read in English](README.en.md)**

## Estado atual da SDK

No estado atual, a SDK expõe:

- `Arara\Config` para configurar autenticação e transporte.
- `Arara\Arara` como cliente principal.
- `Arara::sendMessage()` para envio de mensagens via `POST /messages`.
- Exceções específicas para tratamento de erros HTTP (400, 401, 404, 422, 500).

## Requisitos

- PHP 8.4+
- Extensão JSON
- Composer

## Instalação

```bash
composer require arara/arara-php-sdk
```

Para a documentação completa da API, acesse [docs.ararahq.com](https://docs.ararahq.com).

## Uso rápido

### Criar configuração e cliente

```php
use Arara\Arara;
use Arara\Config;

$config = new Config(
    apiKey: 'sua-api-key',
);

$arara = new Arara($config);
```

### Enviar mensagem

```php
$response = $arara->sendMessage(
    receiver: 'whatsapp:+5511999999999',
    templateName: 'order_confirmation',
    variables: [
        'orderId' => '12345',
        'amount' => 'R$ 199,90',
    ],
);
```

`sendMessage()` retorna um `array` com o JSON de resposta da API.

### Configurações disponíveis

```php
use Arara\Config;

$config = new Config(
    apiKey: 'sua-api-key',
    baseUrl: 'https://api.ararahq.com/api/v1',
    timeout: 30,
    retryTimes: 3,
    retryDelayMs: 100,
);
```

Parâmetros de `Config`:

- `apiKey` (string): token de autenticação Bearer.
- `baseUrl` (string): URL base da API.
- `timeout` (int): timeout de requisição em segundos.
- `retryTimes` (int): tentativas de retry (definido na configuração, ainda não aplicado automaticamente no cliente).
- `retryDelayMs` (int): delay entre retries em ms (definido na configuração, ainda não aplicado automaticamente no cliente).

### Injetar cliente HTTP customizado (opcional)

```php
use Arara\Arara;
use Arara\Config;
use GuzzleHttp\Client;

$config = new Config(apiKey: 'sua-api-key');
$http = new Client();

$arara = new Arara($config, $http);
```

Quando um `Client` customizado é injetado, ele é usado diretamente.

## Tratamento de erros

O SDK lança exceções específicas para cada tipo de erro HTTP:

| Exceção | Status HTTP |
|---------|------------|
| `BadRequestException` | 400 |
| `AuthenticationException` | 401 |
| `NotFoundException` | 404 |
| `ValidationException` | 422 |
| `InternalServerException` | 500 |
| `AraraException` | Outros |

Todas estendem `AraraException`, que expõe `statusCode`, `response` (body decodificado) e `getMessage()`.

```php
use Arara\Exceptions\AraraException;
use Arara\Exceptions\AuthenticationException;
use Arara\Exceptions\ValidationException;

try {
    $arara->sendMessage('whatsapp:+5511999999999', 'welcome');
} catch (AuthenticationException $e) {
    // API key inválida (401)
    echo $e->getMessage();
} catch (ValidationException $e) {
    // Parâmetros inválidos (422)
    print_r($e->response); // detalhes do erro da API
} catch (AraraException $e) {
    // Qualquer outro erro
    echo "Erro {$e->statusCode}: {$e->getMessage()}";
}
```

## Endpoint utilizado

- `POST /messages`

Payload enviado por `sendMessage()`:

```json
{
  "receiver": "whatsapp:+5511999999999",
  "templateName": "order_confirmation",
  "variables": {}
}
```

## Desenvolvimento

```bash
composer install
composer test
composer analyse
composer format
composer check
```

## Licença

MIT. Veja [LICENSE](LICENSE).
