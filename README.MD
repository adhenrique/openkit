## 🚀 OpenKit
**OpenKit** é uma biblioteca elegante com interface fluente para gerar documentação OpenAPI (v3.0) para aplicações Laravel.

Cansado de poluir seus Controllers com centenas de linhas de anotações `#[OA]`? OpenKit resolve esse problema adotando uma filosofia diferente: **Sua documentação de API é um contrato, e deve ser tratada como um cidadão de primeira classe, separada da sua lógica de implementação.**

Com OpenKit, você define sua API em um local central (`routes/openkit.php`) usando uma API fluente e expressiva em PHP puro.

### 🌟 Principais Funcionalidades
- **Interface Fluente Elegante**: Descreva sua API com uma sintaxe encadeada e legível. 
- **Contrato Centralizado**: Mantenha seus Controllers limpos. Toda a sua documentação vive em um só lugar. 
- **Schemas Reutilizáveis**: Defina seus DTOs (`User`, `Product`, etc.) uma vez e referencie-os em qualquer lugar (`$ref`). 
- **Integração com Laravel**: Configuração zero com auto-discovery de `ServiceProvider` e `Facade`. 
- **Suporte Completo**: Define `paths`, `parameters`, `requestBodies`, `responses`, `securitySchemes` (Bearer, API Key) e mais. 
- **Upload de Arquivos**: Suporte nativo para `multipart/form-data`. 
- **Geração sob Demanda**: Gere seu `openapi.json` com um simples comando **artisan**. 
- **UI Pronta para Uso**: Acessa uma bela UI (Swagger UI) em uma rota configurável, sem esforço.

### 📦 Instalação
Você pode instalar o pacote via Composer:
```bash
composer require openkit/openkit
```
#### Configuração
O OpenKit usa o Package Auto-Discovery do Laravel. O `ServiceProvider` e a `Facade` serão registrados automaticamente.

Para publicar o arquivo de configuração, rode:

```bash
php artisan vendor:publish --provider="OpenKit\OpenKitServiceProvider" --tag="openkit-config"
```
Isso criará o arquivo config/openkit.php, onde você pode definir:
- A informação base da sua API (info.title, info.version, etc.). 
- O prefixo da rota da UI (path). 
- O local do seu arquivo de definições (definitions).

## 🏁 Guia Rápido (Getting Started)
### 1. Crie seu Arquivo de Definições
   Por padrão, o OpenKit espera um arquivo em `routes/openkit.php`. Crie este arquivo.
### 2. Defina sua API
   Adicione suas definições usando a facade OpenKit.

`routes/openkit.php`

```php
<?php

use OpenKit\Facades\OpenKit;
use OpenKit\Builders\Parameter;
use OpenKit\Builders\Response;
use OpenKit\Builders\RequestBody;

/*
|--------------------------------------------------------------------------
| Definições Globais
|--------------------------------------------------------------------------
| Defina seus componentes reutilizáveis aqui: Tags, Schemas e Segurança.
*/

// Defina as "categorias" da sua API
OpenKit::defineTag('Users', 'Operações relacionadas a usuários');
OpenKit::defineTag('Products', 'Gerenciamento de produtos');

// Defina seus Schemas (DTOs) reutilizáveis
OpenKit::defineSchema('User', [
    'type' => 'object',
    'properties' => [
        'id' => ['type' => 'integer', 'format' => 'int64'],
        'name' => ['type' => 'string', 'example' => 'João da Silva'],
        'email' => ['type' => 'string', 'format' => 'email'],
    ],
]);

// Defina seus esquemas de segurança
OpenKit::defineBearerAuth('bearerAuth'); // O nome 'bearerAuth' é um ID interno

/*
|--------------------------------------------------------------------------
| Definições de Rotas (Paths)
|--------------------------------------------------------------------------
| Descreva cada endpoint da sua API.
*/

// Exemplo de rota GET
OpenKit::path('/api/users/{id}', 'get')
    ->tag('Users')
    ->summary('Obter um usuário específico')
    ->operationId('getUserById')
    ->securedBy('bearerAuth') // Aplica a segurança definida acima
    
    ->withParameter('id', 'path', function (Parameter $param) {
        $param->description('ID do usuário')
              ->schema(['type' => 'integer']); // 'required' é automático para 'path'
    })
    
    ->withResponse(200, function (Response $res) {
        $res->description('Usuário encontrado')
             // Usa um schema reutilizável
            ->jsonContent(['$ref' => '#/components/schemas/User']);
    })
    ->withResponse(404, function (Response $res) {
        $res->description('Usuário não encontrado');
    });

// Exemplo de rota POST
OpenKit::path('/api/users', 'post')
    ->tag('Users')
    ->summary('Criar um novo usuário')
    ->securedBy('bearerAuth')
    
    ->withRequestBody(function (RequestBody $body) {
        $body->required()
             ->description('Dados do novo usuário')
             // Usa o mesmo schema, mas você também pode definir inline
             ->jsonContent(['$ref' => '#/components/schemas/User']);
    })
    
    ->withResponse(201, function (Response $res) {
        $res->description('Usuário criado com sucesso')
            ->jsonContent(['$ref' => '#/components/schemas/User']);
    });
```

### 3. Gere o JSON
Rode o comando Artisan para processar suas definições e gerar o arquivo JSON.
```bash
php artisan openkit:generate
```
Isso criará o arquivo `openapi.json` na sua pasta `public/` (ou o que estiver definido no config).

### 4. Veja sua Documentação
Visite a rota da UI no seu navegador: `http://seu-app.test/openkit/docs`
(O prefixo `/openkit/docs` pode ser alterado no arquivo config/openkit.php).

## 📚 Referência da API Fluente
### Definições Globais
Use estes métodos no escopo global do seu arquivo de definições.
- `OpenKit::defineTag(string $name, string $description)` Cria uma categoria para agrupar suas rotas. 
- `OpenKit::defineSchema(string $name, array $schema)` Define um schema reutilizável. Você pode usar `$ref` para referenciá-lo: `['$ref' => '#/components/schemas/SeuNome']`. 
- `OpenKit::defineBearerAuth(string $name, string $format = 'JWT')` Define um esquema de segurança `http` (Bearer Token). 
- `OpenKit::defineApiKeyHeader(string $name, string $headerName)` Define um esquema de segurança `apiKey` (ex: `X-API-KEY`). 
- `OpenKit::defineSecurityScheme(string $name, array $definition)` Define qualquer outro esquema de segurança customizado do OpenAPI.

### Definição de Rotas
- `OpenKit::path(string $uri, string $method)` Inicia a definição de uma nova operação (rota). Retorna um `OperationBuilder`.

### OperationBuilder
Métodos disponíveis após chamar `OpenKit::path(...)`.
- `->tag(string $tagName)`: Adiciona a operação a uma Tag (definida globalmente). 
- `->summary(string $summary)`: Um título curto para a operação. 
- `->description(string $description)`: Uma descrição longa. 
- `->operationId(string $id)`: Um ID único (útil para geradores de código). 
- `->deprecated(bool $isDeprecated = true)`: Marca a rota como obsoleta. 
- `->securedBy(string $name, array $scopes = [])`: Aplica um esquema de segurança (definido globalmente) a esta rota.

### Parâmetros (Parâmetros de Rota e Query)
- `->withParameter(string $name, string $in, callable $callback)`
  - `$in`: Onde o parâmetro está. Pode ser `'path'`, `'query'`, `'header'`, ou `'cookie'`.
  - `$callback`: Recebe um `ParameterBuilder`.

```php
->withParameter('status', 'query', function (Parameter $param) {
    $param->description('Filtra por status')
          ->required(false) // Opcional para 'query'
          ->schema([
              'type' => 'string',
              'enum' => ['active', 'inactive']
          ]);
})
```
Métodos do `ParameterBuilder`: `description()`, `required()`, `deprecated()`, `schema()`, `example()`.

### Request Body (Payloads de POST/PUT/PATCH)
- `->withRequestBody(callable $callback)`
  - `$callback`: Recebe um `RequestBodyBuilder`.

```php
->withRequestBody(function (RequestBody $body) {
    $body->required()
         ->description('Payload do usuário');

    // Use jsonContent() para APIs JSON
    $body->jsonContent([
        'type' => 'object',
        'properties' => [...]
    ]);

    // Ou use multipartFormData() para uploads
    $body->multipartFormData([
        'avatar' => ['type' => 'string', 'format' => 'binary'],
        'name' => ['type' => 'string']
    ]);
})
```
Métodos do `RequestBodyBuilder`: `description()`, `required()`, `jsonContent(array $schema)`, `multipartFormData(array $properties)`.

### Respostas (Responses)
-` ->withResponse(int $statusCode, callable $callback)`
  - `$statusCode`: O código HTTP (ex: 200, 201, 404).
  - `$callback`: Recebe um `ResponseBuilder`.

```php
->withResponse(200, function (Response $res) {
    $res->description('Operação bem-sucedida')
        ->jsonContent(['$ref' => '#/components/schemas/User'])
        ->header('X-Rate-Limit', [
            'description' => 'Requisições restantes',
            'schema' => ['type' => 'integer']
        ]);
})

->withResponse(404, function (Response $res) {
    // Respostas sem conteúdo só precisam de descrição
    $res->description('Não encontrado');
})
```
Métodos do `ResponseBuilder`: `description()`, `jsonContent(array $schema)`, `content(string $mimeType, array $schema)`, `header(string $name, array $definition)`.

## 🧪 Testes (para Contribuidores)
O pacote utiliza Orchestra Testbench para testes de feature.

```bash
# Instalar dependências (incluindo --dev)
composer install

# Rodar a suíte de testes
composer test
```

## 🤝 Contribuição
Contribuições são bem-vindas! Por favor, abra uma issue para discutir sua ideia ou um pull request com suas alterações.

## 📄 Licença
O OpenKit é um software de código aberto licenciado sob a Licença MIT.