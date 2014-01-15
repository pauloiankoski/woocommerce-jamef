=== WooCommerce Jamef ===
Contributors: pauloiankoski
Donate link: http://paulor.com.br/doacoes/
Tags: shipping, delivery, woocommerce, jamef
Requires at least: 3.5
Tested up to: 3.8
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds Jamef shipping to the WooCommerce plugin

== Description ==

### Add Jamef shipping to WooCommerce ###

This plugin adds Jamef shipping to WooCommerce.

Please notice that WooCommerce must be installed and active.

### Descrição em Português: ###

Adicione os Jamef como método de entrega em sua loja WooCommerce.

[Jamef](http://www.jamef.com.br/) é um método de entrega brasileiro.

O plugin WooCommerce Jamef foi desenvolvido sem nenhum incentivo da Jamef. Nenhum dos desenvolvedores deste plugin possuem vínculos com esta empresa.

Este plugin foi feito baseado no plugin [WooCommerce Correios](http://plugins.wordpress.org/extend/plugins/woocommerce-correios/) do [Cláudio Sanches](http://profiles.wordpress.org/claudiosanches/).

= Instalação: =

Confira o nosso guia de instalação e configuração da Jamef na aba [Installation](http://wordpress.org/extend/plugins/woocommerce-jamef/installation/).

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* A nossa sessão de [FAQ](http://wordpress.org/extend/plugins/woocommerce-jamef/faq/).
* Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/woocommerce-jamef) (apenas em inglês).
* Ou entre em contato com os desenvolvedores do plugin em nossa [página](http://paulor.com.br/).

== Installation ==

* Upload plugin files to your plugins folder, or install using WordPress built-in Add New Plugin installer;
* Activate the plugin;
* Navigate to WooCommerce -> Settings -> Shipping, choose Jamef and fill settings.

### Instalação e configuração em Português: ###

= Instalação do plugin: =

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
* Ative o plugin.

= Requerimentos: =

Possuir instalado a extensão SimpleXML (que já é instalado por padrão com o PHP 5).

= Configurações do plugin: =

Com o plugin instalado navegue até "WooCommerce" > "Configurações" > "Entrega" > "Jamef".

Nesta tela configure o seu **CNPJ** e **Unidade Jamef de origem**.

Também é possível configurar um **Pacote Padrão** que será utilizando para definir as medidas mínimas do pacote de entraga.

= Configurações dos produtos =

Para que seja possível cotar o frete, os seus produtos precisam ser do tipo **simples** ou **variável** e não estarem marcados com *virtual* ou *baixável* (qualquer outro tipo de produto será ignorado na cotação).

É necessário configurar o **peso** e **dimensões** de todos os seus produtos, caso você queria que a cotação de frete seja exata.
Alternativamente, você pode configurar apenas o peso e deixar as dimensões em branco, pois neste caso serão utilizadas as configurações do **Pacote Padrão** para as dimensões (neste caso pode ocorrer uma variação pequena no valor do frete, pois os Jamef consideram mais o peso do que as dimensões para a cotação).

== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

### FAQ em Português: ###

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado o plugin WooCommerce.
* Possuir instalado em sua hospedagem a extensão de SimpleXML.
* Configurar o seu CNPJ e Unidade da Jamef mais próxima à você nas configurações do plugin.
* Adicionar peso e dimensões nos produtos que pretende entregar.

**Atenção**: É obrigatório ter o **peso** configurado em cada produto para que seja possível cotar o frete de forma eficiente. As dimensões podem ficar em branco e neste caso, serão utilizadas as medidas da opção **Pacote Padrão** da configuração do plugin, mas é **recomendado** que cada produto tenha suas configurações próprias de **peso** e **dimensões**.

= Como é feita a cotação do frete? =

A cotação do frete é feita utilizando o Webservices da Jamef utilizando SimpleXML (que é nativo do PHP 5).

Na cotação do frete é usada Unidade Jamef de Origem, CEP de destino do cliente e a cubagem total dos produtos mais o peso. Desta forma o valor cotado sera o mais próximo possível do real.

Desta forma é necessário adicionar pelo menos o peso em cada produto, pois na falta de dimensões serão utilizadas as configurações do pacote padrão.

= Como resolver o erro "Nenhum método de envio encontrado. Por favor, recalcule seu frete informando seu estado/país e o CEP para verificar se há algum método de envio disponível para sua região." ou o erro "Desculpe, aparentemente não existem métodos de entrega disponíveis para sua localidade (Brasil). Se você precisa de ajuda ou deseja fazer uma negociação para realizar a entrega, entre em contato conosco."? =

Esta é uma mensagem de erro padrão do WooCommerce, ela pode ser gerada por vários problemas.

Segue uma lista dos prováveis erros:

* Os produtos foram cadastros sem peso e dimensões.
* O peso e as dimensões foram cadastrados de forma errada, verifique as configurações de medidas em `WooCommerce > Configurações > Catalogo`.

É possível identificar o erro ligando a opção **Log de depuração** nas configurações dos **Jamef**. Desta forma é gerado um log dentro da pasta `wp-content/plugins/woocommerce/logs/`. Ao ativar esta opção, tente realizar uma cotação de frete e depois verique o arquivo gerado.

Caso apareça no log a mensagem `WP_Error: connect() timed out!` pode acontecer do site da Jamef ter caido ou o seu servidor estar com pouca memória.

= O método de entrega da Jamef não aparecem durante o checkout ou no carrinho? =

Verifique se você realmente ativou as opções de entrega do plugin e faça o mesmo procedimento da questão a cima.

Além de conferir se o carrinho possue produtos do tipo **simples** e **variável** e não estarem marcados com *virtual* ou *baixável*.

= O valor do frete calculado não bateu com o da Jamef? =

Este plugin utiliza o Webservices da Jamef para calcular o frete e quando este tipo de problema acontece geralmente é porque:

1. Foram configuradas de forma errada as opções de peso e dimensões dos produtos na loja.
2. O Webservices da Jamef enviou um valor errado!

== Changelog ==

= 1.0.0 =

* Versão inicial do plugin.

== License ==

WooCommerce Jamef is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

WooCommerce Jamef is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with WooCommerce Jamef. If not, see <http://www.gnu.org/licenses/>.
