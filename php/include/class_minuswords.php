<?php

class MinusWords{
    // сделать отдельно ф-ии для проверки заголовка, описания и полного описания
    
    public function checkText($text_to_parse){
        global $db;
        $text = mb_strtolower($text_to_parse);
        
        $sql = "SELECT `words`, `percent` FROM `dbc_words`;";
        $words = $db->db_fetchone_array($sql, __LINE__, __FILE__);
        $miunuses = explode(',', $words["words"]);
        $ban = 0;
        $min_arr = explode(' ', $text);
        
        preg_match_all("/[а-яА-ЯёЁa-zA-Z]/u", $text, $matchesall);
        preg_match_all("/[а-яА-ЯёЁ]/u", $text, $matches);
        preg_match_all('@((https?://)?([-\\w]+\\.[-\\w\\.]+)+\\w(:\\d+)?(/([-\\w/_\\.]*(\\?\\S+)?)?)*)@', $text, $matchesall_links);
        $all = count($matchesall[0]);
        $allRu = count($matches[0]);
        $all_links = count($matchesall_links[0]);
        $countLetterRu = round(($allRu/$all)*100);
        
        if ($all_links > 0){
            $ban = 1;
        }
        elseif ($countLetterRu < $words["percent"]){
            $ban = 1;
        }
        else{
            foreach ($miunuses as $word){
                $tword = trim($word);
                
                if (strpos($tword, '*') !== false){
                    $tword = str_replace('*', '', $tword);

                    if (mb_strpos($text, $tword,  0, 'UTF-8') !== false){ // если есть слово из минус-слов
                        $ban = 1; // баним
                        
                        break;
                    }
                }
                else if (in_array($tword, $min_arr)) { // та же самая проверка наличия минус слова, только теперь через массив слов обявления
                    $ban = 1;
                    
                    break;
                }

            }
        }
        
        return $ban;
    }
}