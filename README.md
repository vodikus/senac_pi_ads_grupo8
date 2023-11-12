# **Clube de Empréstimo de Livros**

## **Grupo 8**

- [**Clube de Empréstimo de Livros**](#clube-de-empréstimo-de-livros)
  - [**Grupo 8**](#grupo-8)
  - [**Apresentação**](#apresentação)
  - [**Instalação**](#instalação)
    - [**Pré-requisitos**](#pré-requisitos)
    - [**Banco de Dados**](#banco-de-dados)
    - [**Backend**](#backend)
    - [**Frontend**](#frontend)
  - [**Visualização e Testes**](#visualização-e-testes)
  - [**Componentes do Grupo**](#componentes-do-grupo)
  - [**Tecnologias**](#tecnologias)

## **Apresentação**

Este projeto consiste em uma aplicação de uma rede social para empréstimo de livros entre os participantes, elaborado para a disciplina **Projeto Integrador: Desenvolvimento de Sistemas Orientado a dispositivos móveis e baseados na Web** do curso de Análise e Desenvolvimento de Sistemas da Faculdade SENAC.

## **Instalação**

### **Pré-requisitos**
Os pré-requisitos básicos para a utilização deste sistema é possuir os seguintes servidores disponíveis:
- **Banco de Dados**: [`Servidor MySQL`](https://dev.mysql.com/downloads/installer/) versão 5.7 ou superior;
- **Servidor Backend**: Apache 2.4 ou superior com o PHP 7.2 ou superior instalado.
- **Servidor Frontend**: [`NodeJS`](https://nodejs.org/en/download/current) v18.16 ou superior

Para rodar o sistema localmente e contribuir com o projeto, é recomendado utilizar:
- [`UwAmp`](https://www.uwamp.com/en/)
- [`MySQL Workbench`](https://www.mysql.com/products/workbench/)
- [`Postman`](https://www.postman.com/)para realizar os requests
- [`PhpStorm`](https://www.jetbrains.com/pt-br/phpstorm/) como IDE de desenvolvimento
- Como alternativa ao PhpStorm, recomendamos a utilização do [`Visual Studio Code`](https://code.visualstudio.com/)

### **Banco de Dados**
Este projeto utiliza o banco MySQL e para o funcionamento deste projeto é necessário executar os scripts de DDL e DML listados abaixo:

1. Arquivo **sql/01-Estrutura-Inicial.sql** contendo os comandos de criação das tabelas e triggers.
2. Arquivo **sql/02-Carga-Dados.sql** para efetuar a carga dos dados de exemplo.

Para efetuar a instalação do banco de dados da aplicação, é necessário utilizar um cliente para se conectar ao MySQL, como PHPMyAdmin, DBeaver ou o MySQL Workbench, configurar a conexão com o banco de dados e executar os scripts.

O processo de criação do banco de dados também é possivel ser executado através do MySQL Workbench seguindo os seguintes passos:

1. Crie uma nova conexão com o endereço 127.0.0.1 e porta 3306, caso esteja usando UwAmp ou similar. 
2. Caso não tenha trocado a senha, o login padrão é usuário e senha "root".
3. No MySQL Workbench selecione o modelo em docs/modelo-clube-livro.mwb
4. Em seguida, clique no menu Database -> Forward Engineer, selecione a conexão com o servidor MySQL e clique em Next até finalizar o processo.
5. Após o término da criação dos objetos, basta executar o script **sql/02-Carga-Dados.sql** para realizar a inserção dos dados.

### **Backend**

**Configurações do Apache**

As configurações do Apache podem ser realizar através do UwAmp.

A primeira coisa que deve ser feita é habilitar o módulo **mod_rewrite**. No UwAmp isso é feito através do menu "Apache Config". Na janela que for aberta, clicar na aba Modules. Na lista de módulos, selecionar o **mod_rewrite**, que pode estar listado também como **rewrite_module**.

A seguir, deve-se criar um *VirtualHost* direcionando para o caminho onde o diretório backend foi baixado no servidor. Este *VirtualHost* deverá ter as opções abaixo:

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
Onde *ServerName* e *ServerAlias* devem conter o nome do servidor que irá responder a este diretório. Já *CAMINHO-DO-BACKEND* deve ser substituído pelo diretório do backend deste repositório. Por exemplo, **C:\projetos\senac_pi_ads_grupo8\backend**

Na interface do UwAmp, essa configuração pode ser feita na mesma janela do Apache Config, na aba "Virtual Server". Clique no botão **+** e preencha da seguinte forma:

```Host Ip: *
Port: 8080
Server Name: clube-backend
Server Alias: clube-backend
Document Root: [Caminho para a basta "backend" do projeto]
Alias: deixe em branco
Folder: [Caminho para a basta "backend" do projeto], **marque All na opção Allow Override**
Others: deixe em branco
```

Para facilitar a resolução do nome do host, pode-se criar uma entrada no arquivo *C:\Windows\System32\drivers\ETC\hosts* do Windows ou */etc/hosts* do Linux:

> 127.0.0.1	clube-backend clube-frontend

**Configurações do PHP**

O servidor de backend necessita que o PHP esteja instalado corretamente e com o módulo **pdo_mysql** habilitado. No UwAmp isso pode ser feito clicando em PHP Config e, na aba PHP Extensions, selecionando a extensão ``php_pdo_mysql``.

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

> :warning: **Alerta:**  As variáveis de ambiente deversão ser criadas no escopo visivel apenas para o usuário que executa o Apache / PHP. Criar estas variáveis de ambiente global pode expor os dados de conexão, bem como usuário e senha para qualquer usuário que consiga acesso ao sistema operacional.

<br/>

A partir dessas configurações, é possível executar o backend no endpoint ``http://localhost:8080/``

### **Frontend**
Para a configuração do frontend, é necessário ter o [`NodeJS`](https://nodejs.org/en) previamente instalado. Há 2 modos de conseguir testar a aplicação: 

**Através do código-fonte**

O código-fonte está disponível no diretório **frontend**. Após o download do projeto, é necessário editar a variável *backendUrl* contida no arquivo *environment.ts*, localizado no caminho **frontend\src\environments**, preenchendo com a URL onde está executando o servidor de backend. Após o ajuste, abra o prompt de comando e acesse o diretório **frontend**. Em seguida, digite os comandos abaixo para instalar as dependências necessárias e iniciar o servidor de desenvolvimento:

1. npm install -g @angular/cli
2. npm install
3. ng serve --host clube-frontend

<br/>

**Através do release**

Baixar o arquivo **frontend.zip** contendo o release, disponibilizado no caminho https://github.com/vodikus/senac_pi_ads_grupo8/releases/tag/v1.0.0, descompactar no diretório de destino e executar os procedimentos abaixo:
1. npm install -g serve
3. serve -s -l 80 CAMINHO-DO-FRONTEND

<br/>

> :memo: **Nota:**  O release tem fixo como padrão acessar o backend através da URL http://3.84.195.238:8080/

<br/>

## **Visualização e Testes**
O acesso padrão ao frontend deverá ser feito utilizando a URL http://clube-frontend:4200/. Há também uma versão online disponível na URL http://3.84.195.238/.

**Logins pré-cadastrados**

> Usuário: *teste@teste.com.br* Senha: *1234*
>
> Usuário: *teste2@teste.com.br* Senha: *1234*
>
> Usuário: *teste3@teste.com.br* Senha: *1234*
>
> Usuário: *teste4@teste.com.br* Senha: *1234*

**Chamadas para o backend**

Para realizar as chamadas diretamente ao backend, o endpoint utilizado será o do UwAmp: ``http://localhost:8080``, seguido pelo path definido na aplicação. 

Pode-se importar a collection *Clube do Empréstimo de Livro.postman_collection.json*

> :warning: **Alerta:**  Para realização de testes, execute primeiro a chamada de autenticação no endpoint "api/auth/getToken" com os dados de usuário já cadastrado (da listagem acima, ou você pode cadastrar um novo via MySQL Workbench). Com o token em mãos, valide-o no endpoint "api/auth/authToken". Após, ele poderá ser utilizado nos demais requests, passando na aba ``Authorization`` do Postman como ``Bearer Token``.

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
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/angularjs/angularjs-original.svg" height="40" width="52" alt="angularjs logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg" height="40" width="52" alt="typescript logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg" height="40" width="52" alt="bootstrap logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" height="40" width="52" alt="php logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" height="40" width="52" alt="mysql logo"  />
</div>

