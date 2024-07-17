<?php

class SlackClass
{
    private const CURL_OPTION_TIMEOUT = 10;

    public function __construct(
        private ?string $channel = null,
        private ?string $slack_url = null,
        private array $options = []
    ) {
        $this->channel = $channel;
        $this->slack_url = $slack_url;
        $this->setOptions($options);
    }

    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    public function setSlackUrl(string $slack_url): void
    {
        $this->slack_url = $slack_url;
    }

    public function setOptions(?array $options = null): void
    {
        if (!is_null($options) && is_array($options)) {
            $this->slack_options = $options;
        }
    }

    /**
     * Slackに配信
     * @param string $message 配信したいテキスト情報
     * @return void
     */
    public function send(string $message): void
    {
        $this->execPost(
            url:$this->slack_url,
            params:['payload' => json_encode([
                'channel' => $this->channel,
                'text' => $this->forSlackMentionReplace($message)
            ])],
            options:$this->options
        );
    }

    /**
     * cURLを実行し値を返却
     *
     * @param string $url 接続先のURL
     * @param array $params パラメーター
     * @param array $option Curlオプション
     * @return string curl結果
     * @throws Exception curlに失敗した場合に返却
     */
    private static function execPost(string $url, array $params, array $options): string|bool
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_OPTION_TIMEOUT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返り値を文字列で取得

        curl_setopt_array($ch, $options);   // 追加パラメタ設定

        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);  // パラメータの設定

        $result = curl_exec($ch);

        if (CURLE_OK !== curl_errno($ch)) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        // cURLセッションを閉じる
        curl_close($ch);

        return $result;
    }

    /**
     * メッセージ内の特定のメンション設定(@here)を
     * slackAPIの仕様に合わせて置換する。
     *
     * @param string $text 対象のメッセージ
     * @return string 置換後のメッセージ
     */
    private function forSlackMentionReplace($text): string
    {
        return preg_replace('/@([^\\s]+)/', '<!$1>', $text);
    }
}
