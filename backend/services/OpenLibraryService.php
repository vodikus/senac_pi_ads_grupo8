<?php
use GuzzleHttp\Exception\RequestException;

class OpenLibraryService
{
    private static $base_url = "https://openlibrary.org";
    private $books = [
        "olid" => "/books/%s.json",
        "isbn" => "/isbn/%s.json",
        "works" => "/works/%s.json",
    ];
    private $authors = [
        "olid" => "/authors/%s.json",
        "works" => "/authors/%s/works.json",
    ];
    private $search = [
        "title" => "/search.json?title=%s",
        "author" => "/search.json?author=%s",
        "query" => "/search.json?q=%s&fields=key,title,isbn,author_key,author_name,cover_edition_key,edition_key",
    ];
    private $covers = [
        "key" => "/covers/%s/%s-%s.jpg",
    ];

    private $subjects = [
        "subject" => "/subjects/%s.json",
    ];


    private function request($scope, $key, $query)
    {
        $response = "";
        try {
            $headers = [
                'User-Agent' => 'curl/2.0',
                'Accept' => 'application/json'
            ];
            $context = OpenLibraryService::$base_url . sprintf($this->$scope[$key], $query);
            $client = new GuzzleHttp\Client([
                'redirect.disable' => true,
                'verify' => false,
                'headers' => $headers
            ]);

            $response = $client->request('GET', $context);
        } catch (RequestException $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            switch ($e->getResponse()->getStatusCode()) {
                case 404:
                    throw new Exception("Recurso não encontrado");
            }

        }
        return json_decode((string) $response->getBody(), true);
    }


    public function requestSearch($key, $value)
    {
        $arrLivros = $this->request("search", $key, $value);
        if ($arrLivros["numFound"] > 0) {
            foreach ($arrLivros["docs"] as $key => $value) {
                if (array_key_exists('isbn', $value)) {
                    $isbn = preg_grep('/[0-9]{13}/', $value["isbn"]);
                    if (!empty($value["author_name"]) && count($isbn) > 0) {
                        $chave = explode('/', $value["key"])[2];
                        $saida[$chave]['titulo'] = $value["title"];
                        $saida[$chave]['capa'] = @$value["cover_edition_key"];
                        $saida[$chave]['autores'] = $value["author_name"];
                        $saida[$chave]['isbn'] = array_values($isbn)[0];
                    }
                }
            }
        }
        return $saida;
    }

    public function requestBooks($key, $value)
    {
        $arrLivros = $this->request("books", $key, $value);

        $saida['titulo'] = $arrLivros["title"];
        $saida['capa'] = @$arrLivros["covers"][0];
        
        $saida['autores'] = $this->parseAuthors($arrLivros['authors']);
        $saida['isbn'] = @$arrLivros['isbn_13'][0];
        $saida['assuntos'] = @$arrLivros['subjects'];

       return $saida;
    }


    private function parseAuthors($arr_authors) {
        $authors= [];
        foreach ($arr_authors as $value) {
            if (array_key_exists('key', $value)) {
                $author = $this->request("authors", "olid", explode('/', $value['key'])[2]);
                $authors[] = $author['name'];
            } elseif (array_key_exists('author', $value)) {
                $author = $this->request("authors", "olid", explode('/', $value['author']['key'])[2]);
                $authors[] = $author['name'];
            }
        }   
        return $authors;
    }
}