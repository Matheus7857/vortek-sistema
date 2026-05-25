# Instalação no Laragon

## Pré-requisitos
- Laragon instalado (Apache + MySQL + PHP 8.2+)
- Composer instalado

## Passo a passo

### 1. Criar projeto Laravel
Abra o terminal dentro da pasta `C:\laragon\www\` e execute:

```bash
composer create-project laravel/laravel sistema-pedidos
```

### 2. Copiar os arquivos gerados
Copie e substitua as pastas deste projeto dentro de `C:\laragon\www\sistema-pedidos\`:

```
app/
database/
resources/views/
routes/web.php
INSTALACAO.md
```

### 3. Configurar o .env
Copie `.env.example` para `.env` e ajuste:

```
DB_DATABASE=sistema_pedidos
DB_USERNAME=root
DB_PASSWORD=        ← Laragon usa senha vazia por padrão
APP_URL=http://sistema-pedidos.test
```

### 4. Criar o banco de dados
No **phpMyAdmin** (acesse via Laragon → Database):
- Clique em "Novo"
- Nome do banco: `sistema_pedidos`
- Collation: `utf8mb4_unicode_ci`
- Clique em "Criar"

### 5. Rodar migrations e seed
No terminal, dentro de `C:\laragon\www\sistema-pedidos\`:

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### 6. Acessar o sistema
Com o Laragon rodando, acesse: http://sistema-pedidos.test

**Usuários de demonstração:**

| E-mail             | Senha     | Perfil       |
|--------------------|-----------|--------------|
| admin@frios.com    | admin123  | Administrador|
| ana@frios.com      | frios123  | Faturamento  |
| pedro@frios.com    | frios123  | Produção     |

---

## Estrutura do banco de dados

```
users                    → Usuários do sistema (com roles)
rotas                    → Rotas de entrega (R1, R2...)
clientes                 → Clientes/compradores
produtos                 → Catálogo de produtos frios
vendedores               → Operadores/faturistas
pedidos                  → Pedidos (número automático a partir de 1001)
pedido_itens             → Itens de cada pedido
pedido_item_conferencias → Resultado da conferência item a item
```

## Fluxo de status dos pedidos

```
rascunho → enviado → confirmado → producao → conferido → pronto
```
