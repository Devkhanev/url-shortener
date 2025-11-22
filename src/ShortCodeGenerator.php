<?php

namespace Khanev\UrlShortener;

class ShortCodeGenerator{
    private  const CHARSET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private const LENGTH = 6;

    private Database $db;

    public function __construct(Database $db){
        $this->db = $db;
    }

    public function generate():string{
        do{
            $code = $this->randomString(self::LENGTH);
            $exists = $this->db->query("select id from short_urls where short_code = ?", [$code]);
            $exists = !empty($result);
        } while($exists);
        return $code;
    }

    public function randomString(int $length): string{

        $result = '';
        $charsetLength = strlen(self::CHARSET);

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = random_int(0, $charsetLength - 1);
            $result .= self::CHARSET[$randomIndex];
        }
        return $result;
    }


}