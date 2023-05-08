# **Clube de Empréstimo de Livros**

## **Grupo 8**

- [Clube de Empréstimo de Livros](#clube-de-empr-stimo-de-livros)
  * [Apresentação](#apresenta--o)
  * [Instalação](#instala--o)
    + [Pré-requisitos](#pr--requisitos)
    + [Banco de Dados](#banco-de-dados)
    + [Backend](#backend)
    + [Frontend](#frontend)
  * [Visualização](#visualiza--o)
  * [Componentes do Grupo](#componentes-do-grupo)
  * [Tecnologias](#tecnologias)

## **Apresentação**

Este projeto consiste em uma aplicação de uma rede social para empréstimo de livros entre os participantes, elaborado para a disciplina **Projeto Integrador: Desenvolvimento de Sistemas Orientado a dispositivos móveis e baseados na Web** do curso de Análise e Desenvolvimento de Sistemas da Faculdade SENAC.

## **Instalação**

### **Pré-requisitos**
Os pré-requisitos básicos para a utilização deste sistema é possuir os seguintes servidores disponíveis:
- **Banco de Dados:** 
  - Servidor MySQL versão 5.7 ou superior;
- **Servidor Backend:** 
  - Apache 2.4 ou superior com o PHP 7.2 ou superior instalado.
- **Servidor Frontend:**
  - NodeJS v18.16 ou superior

### **Banco de Dados**
Este projeto utiliza o banco MySQL e para o funcionamento deste projeto é necessário efetuar os seguintes passos:

1. Executar o script *sql/01-Estrutura-Inicial.sql* para a criação das tabelas, views e triggers.
2. Executar o script *sql/02-Carga-Inicial.sql* para efetuar a carga dos dados iniciais.
3. Executar o script *sql/03-Carga-Exemplo.sql* para efetuar a carga de dados de exemplo.


### **Backend**

**Configurações do Apache**

Para configurar o Apache, é necessário habilitar o módulo **mod_rewrite** e criar um *VirtualHost* direcionando para o caminho onde o diretório backend foi baixado no servidor. Este *VirtualHost* deverá ter as opções abaixo:

```
  <VirtualHost *:8080>
      DocumentRoot "CAMINHO-DO-BACKEND"
      ServerName "clube-backend"
      ServerAlias "clube-backend"
      <Directory "CAMINHO-DO-BACKEND">
          AllowOverride All
          Options FollowSymLinks Includes Indexes 
      </Directory>
  </VirtualHost>
```
Onde *ServerName* e *ServerAlias* devem conter o nome do servidor que irá responder a este diretório. Para facilitar a resolução do nome do host, pode-se criar uma entrada no arquivo *C:\Windows\System32\drivers\ETC\hosts* do Windows ou */etc/hosts* do Linux:

> 127.0.0.1	clube-backend clube-frontend

"CAMINHO-DO-BACKEND" deve ser substituído pelo diretório do backend deste repositório. Por exemplo, "C:\projetos\senac_pi_ads_grupo8\backend"

Alternativamente

<br/>
<br/>

**Configurações do PHP**

O servidor de backend necessita que o PHP esteja instalado corretamente e com o módulo **pdo_mysql** habilitado.

Dentro da classe *backend/includes/Connection.php* estão as configurações padrões de conexão do sistema, entretanto não é recomendado alterar diretamente os dados ali. Por padrão, os dados de conexão configurados são os seguintes:

>**Host do Banco de Dados:** localhost
>
>**Nome do Banco de Dados (schema):** clube_livros
>
>**Usuário:** clube_livros
>
>**Senha:** senha

<br/>

Caso os dados de conexão sejam diferentes dos padrões, é necessário criar as seguintes variáveis de ambiente contendo os seguintes valores:

>**DBHOST** - *Host do Banco de Dados*
>
>**DBNAME** - *Schema do Banco de Dados*
>
>**DBUSER** - *Usuário do Banco de Dados*
>
>**DBPASS** - *Senha do Banco de Dados*

<br/>
<br/>

> :warning: **Alerta:**  As variáveis de ambiente deversão ser criadas no escopo visivel apenas para o usuário que executa o Apache / PHP. Criar estas variáveis de ambiente global pode expor os dados de conexão, bem como usuário e senha para qualquer usuário que consiga acesso ao sistema operacional.

<br/>

### **Frontend**
Para a configuração do frontend, é necessário ter o NodeJS previamente instalado e há 2 modos de conseguir testar a aplicação. 

**Através do código-fonte**

O código-fonte está disponível no diretório *frontend*. Após o download do projeto, abra o prompt de comando e acesse o diretório \senac_pi_ads_grupo8\frontend. Digitar os seguintes comandos:

1. npm install -g @angular/cli
2. npm install
3. ng serve

<br/>

**Através do release**

Baixar o release no caminho XXXXX, descompactar no diretório de destino e executar os procedimentos abaixo:
1. npm install -g serve
3. serve -l 80

<br/>

> :memo: **Nota:**  O release tem fixo como padrão acessar o backend através da URL ZZZZZ

<br/>

## **Visualização e Testes**
O acesso padrão ao frontend deverá ser feito utilizando a URL http://clube-frontend/

Para realizar as chamadas da API pode importar a collection *Clube do Empréstimo de Livro.postman_collection.json*

<br/>

## **Componentes do Grupo**

[Ivan Martins Pereira](https://github.com/vodikus)   
[Janaína Pereira Ângelo](https://github.com/jainiss)   
[Jean da Rocha Vertuoso](https://github.com/Jean-Vertuoso)   
[João Luís Câmara Gueiral](https://github.com/joaogueiral)   
[Jonatha Moreno Jorge](https://github.com/jonathamoreno)   
[Julio Knach de Bittencourt](https://github.com/juliokn)   

<div align="left">
  <img src="https://github-readme-stats.vercel.app/api?username=vodikus&hide_title=false&hide_rank=false&show_icons=true&include_all_commits=true&count_private=true&disable_animations=false&theme=vue&locale=pt-br&hide_border=true&order=1" height="150" alt="stats graph"  style="float: left; padding: 10px;" />
  
  <img src="https://github-readme-stats.vercel.app/api?username=jainiss&hide_title=false&hide_rank=false&show_icons=true&include_all_commits=true&count_private=true&disable_animations=false&theme=vue&locale=pt-br&hide_border=true&order=1" height="150" alt="stats graph"   style="float: left; padding: 10px;" />
  
  <img src="https://github-readme-stats.vercel.app/api?username=Jean-Vertuoso&hide_title=false&hide_rank=false&show_icons=true&include_all_commits=true&count_private=true&disable_animations=false&theme=vue&locale=pt-br&hide_border=true&order=1" height="150" alt="stats graph"    style="float: left; padding: 10px;" />
  
  <img src="https://github-readme-stats.vercel.app/api?username=joaogueiral&hide_title=false&hide_rank=false&show_icons=true&include_all_commits=true&count_private=true&disable_animations=false&theme=vue&locale=pt-br&hide_border=true&order=1" height="150" alt="stats graph"   style="float: left; padding: 10px;" />
  
  <img src="https://github-readme-stats.vercel.app/api?username=jonathamoreno&hide_title=false&hide_rank=false&show_icons=true&include_all_commits=true&count_private=true&disable_animations=false&theme=vue&locale=pt-br&hide_border=true&order=1" height="150" alt="stats graph"   style="float: left; padding: 10px;" />
  
  <img src="https://github-readme-stats.vercel.app/api?username=juliokn&hide_title=false&hide_rank=false&show_icons=true&include_all_commits=true&count_private=true&disable_animations=false&theme=vue&locale=pt-br&hide_border=true&order=1" height="150" alt="stats graph"   style="float: left; padding: 10px;" />

</div>
<div style="clear: both;"></div>

## **Tecnologias**

<div align="left">
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" height="40" width="52" alt="html5 logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" height="40" width="52" alt="javascript logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg" height="40" width="52" alt="bootstrap logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" height="40" width="52" alt="php logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" height="40" width="52" alt="mysql logo"  />
</div>

