# XLSX Orders Export for WooCommerce

**Descrição**: O AWTD Order Filter (aka XLSX Orders Export for WooCommerce) é um plugin para WordPress que permite aos usuários filtrar e exportar pedidos do WooCommerce em formato XLSX, compatível com o plugin Chwazi - Delivery & Pickup Scheduling for WooCommerce Pro, oferecendo uma interface fácil de usar para busca de pedidos por tipo e data.

## 📋 Sumário

- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Como Usar](#como-usar)
- [Shortcode](#shortcode)
- [Desenvolvimento](#desenvolvimento)
- [Contribuições](#contribuições)
- [Licença](#licença)

## 🚀 Requisitos

- **WordPress**: 5.0 ou superior
- **WooCommerce**: 4.0 ou superior
- **PHP**: 7.4 ou superior
- **Chwazi Delivery & Pickup Scheduling for WooCommerce Pro**: Plugin utilizado para adicionar opções de entrega e retirada ao WooCommerce.

## 💾 Instalação

1. Clone este repositório ou baixe o arquivo ZIP.
2. Extraia os arquivos na pasta `wp-content/plugins/` do seu site WordPress.
3. Ative o plugin através do painel de administração do WordPress em **Plugins** > **Plugins Instalados**.

## 🎮 Como Usar

1. Depois de ativar o plugin, adicione o shortcode `[awtd_order_filter]` a qualquer página ou post onde você deseja exibir o filtro de pedidos.
2. Preencha os campos de filtro e clique em **Filtrar Pedidos** para visualizar os pedidos conforme os critérios definidos.
3. Para exportar os pedidos, clique no botão **Exportar XLSX** após visualizar os resultados.

## 🔧 Shortcode

### `[awtd_order_filter]`

Este shortcode exibe o formulário de filtro e exportação de pedidos. Você pode adicioná-lo em qualquer lugar no seu conteúdo do WordPress (post, página, etc.).

## 🛠 Desenvolvimento

Se você deseja contribuir ou personalizar o plugin, siga as etapas abaixo:

1. Clone o repositório:
   ```bash
   git clone <URL-DO-REPOSITORIO>
   ```
2. Instale as dependências do projeto:
   ```bash
   composer install
   ```
3. Faça as alterações necessárias no código.
4. Teste localmente em um ambiente WordPress com WooCommerce instalado.
5. Crie um Pull Request com as suas alterações.

### Estrutura de Arquivos

```
awtd-order-filter/
├── assets/
│   ├── css/
│   │   └── styles.css
│   └── js/
│       └── scripts.js
├── includes/
│   └── export-orders.php
├── vendor/
│   └── (Arquivos gerados pelo Composer)
├── awtd-order-filter.php
├── composer.json
└── README.md
```

- **assets/**: Contém os arquivos CSS e JavaScript do plugin.
- **includes/**: Contém as funções principais que lidam com o filtro e exportação de pedidos.
- **awtd-order-filter.php**: Arquivo principal do plugin que inicializa o shortcode e enfileira os scripts.
- **composer.json**: Arquivo de configuração para instalar dependências como PhpSpreadsheet.

## 🤝 Contribuições

Contribuições são bem-vindas! Sinta-se à vontade para abrir Issues e Pull Requests no repositório. Para contribuições maiores, por favor, abra uma Issue para discutir o que você gostaria de mudar antes de fazer qualquer modificação.

## 📜 Licença

Este plugin é licenciado sob a licença GPL-3.0. Consulte o arquivo `LICENSE` para mais informações.
