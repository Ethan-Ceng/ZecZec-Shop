/**
 * Alipay.com Inc.
 * Copyright (c) 2004-2020 All Rights Reserved.
 */
package com.alipay.easysdk.kernel.util;

import com.alipay.easysdk.kernel.AlipayConstants;
import org.bouncycastle.util.encoders.Base64;

import javax.crypto.Cipher;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;

/**
 * 加密工具
 */
public class AES {
    private static final String AES_ALG         = "AES";
    private static final String AES_CBC_PCK_ALG = "AES/CBC/PKCS5Padding";
    private static final byte[] AES_IV          = initIV();

    /**
     * AES加密
     *
     * @param plainText 明文
     * @param key       對稱金鑰
     * @return 密文
     */
    public static String encrypt(String plainText, String key) {
        try {
            Cipher cipher = Cipher.getInstance(AES_CBC_PCK_ALG);

            IvParameterSpec iv = new IvParameterSpec(AES_IV);
            cipher.init(Cipher.ENCRYPT_MODE,
                    new SecretKeySpec(Base64.decode(key.getBytes()), AES_ALG), iv);

            byte[] encryptBytes = cipher.doFinal(plainText.getBytes(AlipayConstants.DEFAULT_CHARSET));
            return new String(Base64.encode(encryptBytes));
        } catch (Exception e) {
            throw new RuntimeException("AES加密失敗，plainText=" + plainText +
                    "，keySize=" + key.length() + "。" + e.getMessage(), e);
        }
    }

    /**
     * 密文
     *
     * @param cipherText 密文
     * @param key        對稱金鑰
     * @return 明文
     */
    public static String decrypt(String cipherText, String key) {
        try {
            Cipher cipher = Cipher.getInstance(AES_CBC_PCK_ALG);
            IvParameterSpec iv = new IvParameterSpec(AES_IV);
            cipher.init(Cipher.DECRYPT_MODE, new SecretKeySpec(Base64.decode(key.getBytes()), AES_ALG), iv);

            byte[] cleanBytes = cipher.doFinal(Base64.decode(cipherText.getBytes()));
            return new String(cleanBytes, AlipayConstants.DEFAULT_CHARSET);
        } catch (Exception e) {
            throw new RuntimeException("AES解密失敗，cipherText=" + cipherText +
                    "，keySize=" + key.length() + "。" + e.getMessage(), e);
        }
    }

    /**
     * 初始向量的方法，全部為0
     * 這裡的寫法適合於其它演算法，AES演算法IV值一定是128位的(16位元組)
     */
    private static byte[] initIV() {
        try {
            Cipher cipher = Cipher.getInstance(AES_CBC_PCK_ALG);
            int blockSize = cipher.getBlockSize();
            byte[] iv = new byte[blockSize];
            for (int i = 0; i < blockSize; ++i) {
                iv[i] = 0;
            }
            return iv;
        } catch (Exception e) {
            int blockSize = 16;
            byte[] iv = new byte[blockSize];
            for (int i = 0; i < blockSize; ++i) {
                iv[i] = 0;
            }
            return iv;
        }
    }
}