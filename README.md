# Arara PHP SDK

SDK PHP oficial para integração com a API de WhatsApp da [Arara](https://ararahq.com).

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

> **[Read in English](README.en.md)**

## Sobre

Este SDK permite integrar facilmente sua aplicação PHP com a API da Arara para envio de mensagens via WhatsApp, gerenciamento de templates e configuração de webhooks.

## Requisitos

- PHP 8.2 ou superior
- Extensão JSON
- Composer

## Instalação

```bash
composer require arara/php-sdk
```

## Começando

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

// Usando array
$response = $arara->messages()->send([
    'to' => '5511999999999',
    'template_name' => 'order_confirmation',
    'parameters' => [
        'nome' => 'João Silva',
        'pedido' => '12345',
        'valor' => 'R$ 150,00',
    ],
    'language' => 'pt_BR',
]);

// Usando DTO
$message = new SendMessageData(
    to: '5511999999999',
    templateName: 'order_confirmation',
    parameters: ['nome' => 'João'],
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
    echo $template->name . ' - ' . $template->status . PHP_EOL;
}

// Obter template específico
$template = $arara->templates()->get('order_confirmation');

if ($template->isApproved()) {
    echo "Template aprovado!";
}
```

### Gerenciar Webhooks

```php
// Criar webhook
$webhook = $arara->webhooks()->create([
    'url' => 'https://meusite.com/webhook/arara',
    'events' => ['message.delivered', 'message.read', 'cart.updated'],
]);

echo "Webhook criado com ID: " . $webhook->id;

// Listar webhooks
$webhooks = $arara->webhooks()->list();

// Atualizar webhook
$arara->webhooks()->update($webhook->id, [
    'events' => ['message.delivered'],
]);

// Remover webhook
$arara->webhooks()->delete($webhook->id);
```

### Helper para Telefones Brasileiros

```php
use Arara\Support\PhoneNumber;

// Formatar para padrão E.164
PhoneNumber::format('11999999999');      // +5511999999999
PhoneNumber::format('(11) 99999-9999');  // +5511999999999

// Validar número brasileiro
PhoneNumber::isValid('11999999999');     // true
PhoneNumber::isValid('1199999999');      // false (faltando dígito)

// Detectar tipo
PhoneNumber::isMobile('11999999999');    // true
PhoneNumber::isLandline('1133334444');   // true

// Formatar para exibição
PhoneNumber::formatForDisplay('5511999999999'); // (11) 99999-9999
```

## Tratamento de Erros

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
    // API Key inválida ou ausente (401)
    echo "Erro de autenticação: " . $e->getMessage();
} catch (ValidationException $e) {
    // Dados inválidos (422)
    echo "Erro de validação: " . $e->getMessage();
    print_r($e->errors); // Detalhes dos erros
} catch (RateLimitException $e) {
    // Rate limit excedido (429)
    echo "Rate limit excedido. Aguarde " . $e->retryAfter . " segundos.";
} catch (ApiException $e) {
    // Outros erros da API
    echo "Erro: " . $e->getMessage() . " (Status: " . $e->statusCode . ")";
}
```

## Estrutura do Projeto

```
src/
├── Arara.php                    # Cliente principal (entry point)
├── AraraClient.php              # Cliente HTTP interno
├── Config.php                   # Configurações do SDK
├── Contracts/                   # Interfaces
├── DTOs/                        # Data Transfer Objects
│   ├── Message/
│   ├── Template/
│   └── Webhook/
├── Exceptions/                  # Exceções customizadas
├── Http/                        # Cliente HTTP (Guzzle)
├── Resources/                   # Resources da API
│   ├── Messages.php
│   ├── Templates.php
│   └── Webhooks.php
└── Support/                     # Helpers
    └── PhoneNumber.php
```

## Desenvolvimento

Este projeto foi desenvolvido com auxílio do [Claude Code](https://claude.ai/code), uma ferramenta de IA da Anthropic. Todo o código foi revisado e validado pelo mantenedor.

### Instalar dependências

```bash
composer install
```

### Rodar testes

```bash
composer test
# ou
./vendor/bin/phpunit
```

### Análise estática

```bash
composer analyse
# ou
./vendor/bin/phpstan analyse src tests --level=8
```

### Formatar código

```bash
composer format
# ou
./vendor/bin/php-cs-fixer fix
```

### Todos os checks

```bash
composer check
```

## Como Contribuir

1. Faça um **Fork** deste repositório
2. Crie uma **Branch** para sua feature (`git checkout -b feat/nova-feature`)
3. Faça suas alterações e **Commits**
4. Faça o **Push** para o seu fork (`git push origin feat/nova-feature`)
5. Abra um **Pull Request**

### Padrões de Código

- PSR-12
- Strict types em todos os arquivos
- Type hints em todos os parâmetros e retornos
- PHPStan level 8

## Documentação da API

Para mais informações sobre a API da Arara, consulte a [documentação oficial](https://docs.ararahq.com/api-reference).

## Licença

Este projeto está licenciado sob a [Licença MIT](LICENSE).
