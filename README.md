<h1>Documentação Sistema Desafio desenvolvedor Oliveira Trust </h1>

<h2> Executar o sistema </h2>

<p> O repositório possui docker-compose.yml configurado e .env incluso no repositório com as credenciais do container do banco de dados, buscando facilitar a execução e testes; para testar o sistema
  apenas rodar: docker compose up -d; o container usa a porta 9000</p>

<p> O projeto conta com três containers:
  <ul>
    <li>laravel-app onde está o laravel e tbm o servidor executando artisan serve</li>
    <li> laravel-db onde está o banco de dados mysql </li>
    <li> php-queue, container criado para executar as filas automaticamente</li>
  </ul> </p>

<h3> Endpoints </h3>

<p> O sistema possui 4 endpoints; são eles:</p>

<h4>post /api/v1/upload</h4>
<p>
  endpoint para upload do arquivo csv ou xlsx; recebe apenas um arquivo no request file;
  A rota chama o UploadController que valida o arquivo e salva suas informações na tabela uploads, e então envia o processamento do arquivo para um job em fila, 
  o job processa a planilha e salva suas informações em lote na tabela records;
</p>

<h4> get /api/v1/historic</h4>

<p> esta rota lista todo o historico de uploads de forma páginada, com o nome do arquivo e o safename, usado como nome real para não sobescrever possiveis arquivos com o mesmo nome na storage</p>

<h4> post /api/v1/uploads/search</h4>

<p> Recebe os parâmetros fileName e date, ambos opcionais, procura uploads com os dados requisitados de forma páginada, se não receber nenhum parâmetro, retorna todos os arquivos, 
  aqui você pode ver o safename que pode ser usado na próxima requisição...</p>

<h4> post /api/v1/uploads/{safeName}</h4>

<p> Recebe na url o safeName do arquivo que se busca, se não enviado nenhum parâmetro retorna todos os records da tabela relacionados ao arquivo requisitado;
recebe opcionalmente os parâmetros: RptDt e TckrSymb e busca os dados da referida linha na tabela</p>
