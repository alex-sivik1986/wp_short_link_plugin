<?php

if(!class_exists('OriginalLinkValidate'))
{
    require_once (  dirname(__FILE__) .'/original_validate.php');
}

class CreateShortUrl
{
    private int $long = 5;
    public string $original;
    public string $short_result;

    public function __construct($original)
    {
        $this->original = $original;
    }

    /**
     * @throws Exception
     */
    public function validateDataForm($original)
    {
        $check = new OriginalLinkValidate();

        try {
            $validate = $check->validated_link($original);
            return $validate;
        }
        catch(Exception $e) {
            echo $e->getMessage(); die;
        }
    }

    /**
     * @throws Exception
     */
    private function convertOriginalLink($link): string
    {
        return substr(strtolower(preg_replace('/[0-9_\/]+/','',base64_encode(md5($link)))),0,$this->long);
    }

    private function checkedInTableSql($short)
    {
        global $wpdb;
        $shortLinkSql = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}short_link WHERE link_short = %s", $short ), ARRAY_A );
        return $shortLinkSql;
    }

    public function getResult()
    {
        $result = $this->validateDataForm($this->original);

        if(!is_array($result))
        {
            $short = $this->convertOriginalLink($result);
            $sql = $this->checkedInTableSql($short);
            if($sql == null) {
                global $wpdb;
                $data = array('link_original' => $result, 'link_short' => $short, 'created_at' => current_time('mysql'));
                $wpdb->insert($wpdb->prefix.'short_link',$data);
                return ['link_short' => 'Ваше скорочене посилання: '. home_url() . '/' .$short];
            } else {
                $sql['link_short'] = 'Ваше скорочене посилання: '. home_url() . '/' .$sql['link_short'];
                return $sql;
            }
        } else {
            $result['link_short'] = 'Ваше скорочене посилання: '. home_url() . '/' .$result['link_short'];
            return $result;
        }
    }
}
