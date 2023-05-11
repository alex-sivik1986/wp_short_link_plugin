<?php

final class OriginalLinkValidate
{
    /**
     * @throws Exception
     */
    public function validated_link($link)
    {
        if(empty($link))
        {
            return [
                'response' => 'error',
                'message' => 'ПОМИЛКА: Поле пусте'
            ];
        }

        if($this->validateFormat($link) === false)
        {
            return [
                'response' => 'error',
                'message' => 'ПОМИЛКА: Не вірний формат посилання'
            ];
        }

        if($this->checkLink($link) === false)
        {
            return [
                'response' => 'error',
                'message' => 'ПОМИЛКА: Посилання не дійсне, код 404'
            ];
        }

        return $link;
    }

    private function validateFormat($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL, FILTER_VALIDATE_DOMAIN );
    }

    private function checkLink($url): bool
    {
        $headers = get_headers($url);
        if(($headers && strpos( $headers[0], '404')) || $headers == false) {
            return false;
        } else {
            return true;
        }
    }
}
