<?php
namespace Khanev\UrlShortener;

class UrlShortener
{

    private Database $db;
    private ShortCodeGenerator $generator;

    public function __construct(Database $db, ShortCodeGenerator $generator){
        $this->db = $db;
        $this->generator = $generator;
    }

    public function shorten(string $url):array{

        $shortCode = $this->generator->generate();
        $this->db->execute("insert into short_urls(url, short_code, access_count) values (?,?,?)", [$url, $shortCode, 0]);
        $id = $this->db->lastInsertId();

        return [
            'id' => $id,
            'url' => $url,
            'short_code' => $shortCode,
        ];
    }

    public function getUrl(string $shortCode): ?string{
        $result = $this->db->query("select url from short_urls where short_code = ?", [$shortCode]);

        if(empty($result)){
            return null;
        }

        return $result[0]['url'];
    }

    public function incrementAccessCount(string $shortCode) :void{
        $this->db->execute("update short_urls set access_count = access_count + 1 where short_code = ?", [$shortCode]);
    }
}