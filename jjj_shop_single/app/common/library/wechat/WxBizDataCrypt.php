<?php

/**
 * 對微信小程式使用者加密資料的解密示例程式碼.
 *
 * @copyright Copyright (c) 1998-2014 Tencent Inc.
 */


namespace app\common\library\wechat;


class WxBizDataCrypt
{
    private $appid;
	private $sessionKey;

	/**
	 * 建構函式
	 * @param $sessionKey string 使用者在小程式登入後獲取的會話金鑰
	 * @param $appid string 小程式的appid
	 */
	public function __construct( $appid, $sessionKey)
	{
		$this->sessionKey = $sessionKey;
		$this->appid = $appid;
	}


	/**
	 * 檢驗資料的真實性，並且獲取解密後的明文.
	 * @param $encryptedData string 加密的使用者資料
	 * @param $iv string 與使用者資料一同返回的初始向量
	 * @param $data string 解密後的原文
     *
	 * @return int 成功0，失敗返回對應的錯誤碼
	 */
	public function decryptData( $encryptedData, $iv, &$data )
	{
		if (strlen($this->sessionKey) != 24) {
			return ErrorCode::$IllegalAesKey;
		}
		$aesKey=base64_decode($this->sessionKey);

        
		if (strlen($iv) != 24) {
			return ErrorCode::$IllegalIv;
		}
		$aesIV=base64_decode($iv);

		$aesCipher=base64_decode($encryptedData);

		$result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

		$dataObj=json_decode( $result );
		if( $dataObj  == NULL )
		{
			return ErrorCode::$IllegalBuffer;
		}
		if( $dataObj->watermark->appid != $this->appid )
		{
			return ErrorCode::$IllegalBuffer;
		}
		$data = $result;
		return ErrorCode::$OK;
	}

}

